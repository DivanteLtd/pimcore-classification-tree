pimcore.registerNS("pimcore.plugin.DivanteClassificationTreeBundle");

pimcore.plugin.DivanteClassificationTreeBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.DivanteClassificationTreeBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("DivanteClassificationTreeBundle ready!");
    }
});

var DivanteClassificationTreeBundlePlugin = new pimcore.plugin.DivanteClassificationTreeBundle();
