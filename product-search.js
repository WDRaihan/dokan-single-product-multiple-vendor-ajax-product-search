((o) => {
    const t = {
        init: () => {
            o("#dokan-spmv-area-toggle-button").on("click", t.toggleBoxContent),
                o(".dokan-spmv-add-new-product-search-box-area.section-closed .info-section").on("click", () => {
                    o(".dokan-spmv-add-new-product-search-box-area").hasClass("section-closed") && t.toggleBoxContent();
                }),
                o(document).on("click", "button.dokan-spmv-clone-product", t.processProductCloning);
        },
        toggleBoxContent: () => {
            o(".dokan-spmv-add-new-product-search-box-area").toggleClass("section-closed");
        },
        processProductCloning: (t) => {
            t.preventDefault();
            const e = o("#dokan-spmv-product-list-table"),
                n = o(t.target).data("product"),
                a = e.data("security");
            e.block({ message: null, overlayCSS: { background: "#fff", opacity: 0.6 } }),
                o.post(dokan.ajaxurl, { action: "dokan_spmv_handle_product_clone_request", nonce: a, product_id: n }, (o) => {
                    o.success
                        ? (dokan_sweetalert(o.data.message, { position: "bottom-end", toast: !0, icon: "success", showConfirmButton: !1, timer: 2e3, timerProgressBar: !0 }), e.unblock(), window.location.replace(o.data.url))
                        : (dokan_sweetalert(o.data, { position: "bottom-end", toast: !0, icon: "error", showConfirmButton: !1, timer: 2e3, timerProgressBar: !0 }), e.unblock());
                });
        },
    };
    o(window).on("load", () => {
        t.init();
    }),
        o("body").on("dokan-product-editor-popup-opened", () => {
            t.init();
        });
})(jQuery);
