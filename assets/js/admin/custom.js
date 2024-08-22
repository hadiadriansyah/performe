"use strict";

var KTAlertDialog = function () {
    const showSuccessMessage = (message, onSuccess) => {
        Swal.fire({
            text: message,
            icon: "success",
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: {
                confirmButton: "btn btn-primary"
            }
        }).then(() => {
            if (typeof onSuccess === 'function') {
                onSuccess();
            }
        });
    };

    const showErrorMessage = (message, onError) => {
        Swal.fire({
            text: message,
            icon: "error",
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: {
                confirmButton: "btn btn-primary"
            }
        }).then(() => {
            if (typeof onError === 'function') {
                onError();
            }
        });
    };

    const showConfirmationDialog = (message, onConfirm) => {
        Swal.fire({
            text: message,
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Yes, Do it!",
            cancelButtonText: "No, return",
            customClass: {
                confirmButton: "btn btn-primary",
                cancelButton: "btn btn-active-light"
            }
        }).then((result) => {
            if (result.value) {
                onConfirm();
            } else if (result.dismiss === 'cancel') {
                showErrorMessage("Your form has not been cancelled!.");
            }
        });
    };

    const showInfoHtml = (message, html, onConfirm) => {
        Swal.fire({
            title: message,
            icon: "info",
            html: html,
            buttonsStyling: false,
            confirmButtonText: "Ok, got it!",
            customClass: {
                confirmButton: "btn btn-primary"
            }
        }).then(() => {
            if (typeof onConfirm === 'function') {
                onConfirm();
            }
        });
    };

    return {
        showSuccessMessage: showSuccessMessage,
        showErrorMessage: showErrorMessage,
        showConfirmationDialog: showConfirmationDialog,
        showInfoHtml: showInfoHtml
    };
}();

var KTLMenuAdmin = function() {
    function getDesiredPath(path = '') {
        try {
            var fullPath = path ? new URL(path).pathname : location.pathname;
            var pathParts = fullPath.split('/');

            if (location.hostname === 'localhost') {
                return '/' + pathParts.slice(2).join('/');
            }

            return fullPath;
        } catch (error) {
            return null;
        }
    }

    function addActiveClass(element) {
        var currentPath = getDesiredPath() + '/';
        var attrHref = getDesiredPath(element.getAttribute('href')) + '/';
        
        if (currentPath.indexOf(attrHref) !== -1) {
            element.closest('.menu-link').classList.add('active');
            if (element.closest('.menu-sub-lg-down-accordion')) {
                element.closest('.menu-sub-lg-down-accordion').parentElement.classList.add('show');
                element.closest('.menu-sub-lg-down-accordion').parentElement.parentElement.classList.add('here', 'show');
                element.closest('.menu-sub-lg-down-accordion').parentElement.parentElement.parentElement.classList.add('here', 'show');
            } 
            else if (element.closest('.menu-sub')) {
                element.closest('.menu-accordion').classList.add('here', 'show');
            }
        }
    }

    var handleActiveMenu = function() {
        var menuItems = document.querySelectorAll('.menu-item a');
        menuItems.forEach(function(item) {
            addActiveClass(item);
        });

        var topMenuItems = document.querySelectorAll('.menu-lg-down-accordion .menu-item a');
        topMenuItems.forEach(function(item) {
            var currentPath = getDesiredPath() + '/';
            var attrHref = getDesiredPath(item.getAttribute('href')) + '/';
            
            if (currentPath.indexOf(attrHref) !== -1) {
                var topMenu = item.closest('.menu-lg-down-accordion');
                if (topMenu) {
                    topMenu.classList.add('here', 'show');
                }
            }
        });
    }

    return {
        init: function() {
            handleActiveMenu();
        }
    };
}();

var KTPageLoader = function () {
    const createPageLoading = function () {
        let loadingEl = document.querySelector(".page-loader");
        if (!loadingEl) {
            loadingEl = document.createElement("div");
            document.body.prepend(loadingEl);
            loadingEl.classList.add("page-loader");
            loadingEl.classList.add("flex-column");
            loadingEl.classList.add("bg-dark");
            loadingEl.classList.add("bg-opacity-50");
            loadingEl.innerHTML = `
                <img alt="Logo" src="assets/media/logo-blue.png" style="max-height: 50px;">
                <div class="d-flex align-items-center mt-5">
                    <span class="spinner-border text-primary" role="status"></span>
                    <span class="text-muted fs-6 fw-semibold ms-5">Loading...</span>
                </div>
            `;
        }
    }

    const removePageLoading = function () {
        let loadingEl = document.querySelector(".page-loader");
        if (loadingEl) {
            loadingEl.remove();
        }
    }

    const showPageLoading = function () {
        document.body.classList.add("page-loading"),
        document.body.setAttribute("data-kt-app-page-loading", "on");
    }
    
    const hidePageLoading = function () {
        document.body.classList.remove("page-loading"),
        document.body.removeAttribute("data-kt-app-page-loading");
    }

    return {
        createPageLoading: createPageLoading,
        showPageLoading: showPageLoading,
        hidePageLoading: hidePageLoading,
        removePageLoading: removePageLoading
    };
}();

KTUtil.onDOMContentLoaded(function() {
    KTLMenuAdmin.init();
});