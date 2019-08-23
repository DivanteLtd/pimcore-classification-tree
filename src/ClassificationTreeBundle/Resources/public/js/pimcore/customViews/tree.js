pimcore.registerNS("pimcore.plugin.DivanteClassificationTreeBundle.customviews.customview");

pimcore.object.customviews.tree = Class.create(pimcore.object.customviews.tree , {


    init: function (rootNodeConfig) {
        if (this.config.treeTitle == 'Classification Tree') {
            this.treeDataUrl = "/admin/classification-tree/tree-get-childs-by-id";
        }
        var itemsPerPage = 30;
        rootNodeConfig.text = t("home");
        rootNodeConfig.id = "" +  rootNodeConfig.id;
        rootNodeConfig.allowDrag = true;
        rootNodeConfig.iconCls = "pimcore_icon_home";
        rootNodeConfig.expanded = true;
        if (this.config.treeTitle == 'Classification Tree') {
            var store = Ext.create('pimcore.plugin.DivanteClassificationTreeBundle.PagingTreeStore', {
                autoLoad: true,
                autoSync: false,
                //model: 'pimcore.data.PagingTreeModel',
                proxy: {
                    type: 'ajax',
                    url: this.treeDataUrl,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',

                        rootProperty: 'nodes'
                    },
                    extraParams: {
                        limit: itemsPerPage,

                        view: this.config.customViewId
                    }
                },
                pageSize: itemsPerPage,
                root: rootNodeConfig
            });
        } else {
            var store = Ext.create('pimcore.data.PagingTreeStore', {
                autoLoad: true,
                autoSync: false,
                //model: 'pimcore.data.PagingTreeModel',
                proxy: {
                    type: 'ajax',
                    url: this.treeDataUrl,
                    reader: {
                        type: 'json',
                        totalProperty: 'total',
                        rootProperty: 'nodes'
                    },
                    extraParams: {
                        limit: itemsPerPage,
                        view: this.config.customViewId
                    }
                },
                pageSize: itemsPerPage,
                root: rootNodeConfig
            });
        }


        // objects
        this.tree = Ext.create('pimcore.tree.Panel', {
            store: store,
            region: "center",
            autoLoad: false,
            iconCls: this.config.treeIconCls,
            id: this.config.treeId,
            title: this.config.treeTitle,
            autoScroll: true,
            animate: false,
            rootVisible: true,
            bufferedRenderer: false,
            border: false,
            listeners: this.getTreeNodeListeners(),
            scrollable: true,
            viewConfig: {
                plugins: {
                    ptype: 'treeviewdragdrop',
                    appendOnly: false,
                    ddGroup: "element",
                    scrollable: true
                },
                listeners: {
                    nodedragover: this.onTreeNodeOver.bind(this)
                },
                xtype: 'pimcoretreeview'
            },
            tools: [{
                type: "right",
                handler: pimcore.layout.treepanelmanager.toRight.bind(this),
                hidden: this.position == "right"
            },{
                type: "left",
                handler: pimcore.layout.treepanelmanager.toLeft.bind(this),
                hidden: this.position == "left"
            }],
            root: rootNodeConfig
        });

        store.on("nodebeforeexpand", function (node) {
            pimcore.helpers.addTreeNodeLoadingIndicator("object", node.data.id);
        });

        store.on("nodeexpand", function (node, index, item, eOpts) {
            pimcore.helpers.removeTreeNodeLoadingIndicator("object", node.data.id);
        });


        this.tree.on("afterrender", function () {
            this.tree.loadMask = new Ext.LoadMask(
                {
                    target: Ext.getCmp(this.config.treeId),
                    msg:t("please_wait")
                });
        }.bind(this));

        this.config.parentPanel.insert(this.config.index, this.tree);
        this.config.parentPanel.updateLayout();


        if (!this.config.parentPanel.alreadyExpanded && this.perspectiveCfg.expanded) {
            this.config.parentPanel.alreadyExpanded = true;
            this.tree.expand();
        }

    },

    onTreeNodeClick: function (tree, record, item, index, event, eOpts ) {
        if (event.ctrlKey === false && event.shiftKey === false && event.altKey === false) {
            try {
                if (record.data.permissions.view) {
                    var id = record.data.id;
                    if (this.config.title == 'Classification Tree') {
                        id = id.split('-')[1];
                    }
                    pimcore.helpers.openObject(id, record.data.type);
                }
            } catch (e) {
                console.log(e);
            }
        }
    }
});