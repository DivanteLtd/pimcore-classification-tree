Ext.define('pimcore.plugin.DivanteClassificationTreeBundle.PagingTreeStore', {

    extend: 'Ext.data.TreeStore',

    ptb: false,
    onProxyLoad: function(operation) {
        try {
            var me = this;
            var options = operation.initialConfig
            var node = options.node;
            var proxy = me.getProxy();
            proxy.setExtraParam("fromPaging", 0);
            var extraParams = proxy.getExtraParams();
            var response = operation.getResponse();
            var data = Ext.decode(response.responseText);

            node.fromPaging = data.fromPaging;


            var total = data.total;

            var text = node.data.text;
            if (typeof total == "undefined") {
                total = 0;
            }
            node.addListener("expand", function (node) {
                var tree = node.getOwnerTree();
                if (tree) {
                    var view = tree.getView();
                    view.updatePaging();
                }
            }.bind(this));


            //to hide or show the expanding icon depending if childs are available or not
            node.addListener('remove', function (node, removedNode, isMove) {
                if (!node.hasChildNodes()) {
                    node.set('expandable', false);
                }
            });
            node.addListener('append', function (node) {
                node.set('expandable', true);
            });
            me.addListener('beforeLoad', function(store, operation, eOpts) {
                var node = operation.node;
                var parent = node;
                var payload = node.data.text.substring(0,8);
                while (intval(parent.parentNode.id) != 1) {
                    parent = parent.parentNode;
                }
                operation._proxy.setExtraParam('classificationName', parent.data.text);
                operation._proxy.setExtraParam('nodeName', payload);
            });
            if (me.pageSize < total) {
                node.needsPaging = true;
                node.pagingData = {
                    total: data.total,
                    offset: data.offset,
                    limit: data.limit
                }
            }
            me.superclass.onProxyLoad.call(this, operation);
            var proxy = this.getProxy();
            proxy.setExtraParam("start", 0);
        } catch (e) {
            console.log(e);
        }
    }
});
