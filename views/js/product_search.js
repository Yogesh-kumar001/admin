/**
 * 2007-2025 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2025 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

// Global variables
let page = 1;
let isLoading = false;
let productContainer, loadTrigger, loadingIndicator, observer;

// SweetAlert2 helper functions
function showSuccessMessage(message) {
  Swal.fire({
    position: top_end,
    icon: success,
    text: message,
    timer: 1000,
    timerProgressBar: true,
    width: 300,
    showConfirmButton: false,
  });
}

function showErrorMessage(message) {
  Swal.fire({
    icon: error,
    title: Error,
    text: message,
  });
}

// Function to reload rules
function reloadRules() {
  $.ajax({
    url:
      "index.php?controller=AdminMerchandising&ajax=1&action=loadRules&token=" +
      token,
    type: "POST",
    data: {
      id_awvisualmerchandising: id_awvisualmerchandising,
    },
    success: function (response) {
      $("#addedRulesContainer").html(response);
      rulesSoratable();
    },
  });
}

// Function to reload list products
function reloadListProducts() {
  productContainer = $("#product_list");
  productContainer.empty();
  page = 0;
  loadMoreProducts();
}

//load pinned products (duplicate if needed)
function loadPinnedProducts() {
  $.ajax({
    url:
      "index.php?controller=AdminMerchandising&ajax=1&action=getPinnedProducts&token=" +
      token,
    method: "GET",
    data: { id_awvisualmerchandising: id_awvisualmerchandising },
    success: function (response) {
      $("#pinned-products-wrapper").html(response);
      initPinnedSortable();
      updateUIProducts();
    },
    error: function () {
      console.error("An error occurred while fetching pinned products.");
    },
  });
}

// Function to load more products based on the current page number
function loadMoreProducts() {
  if (isLoading) return;
  isLoading = true;
  loadingIndicator.show();

  $.ajax({
    url:
      "index.php?controller=AdminMerchandising&ajax=1&action=getCategoryProduct&token=" +
      token +
      "&page=" +
      page,
    method: "GET",
    dataType: "json",
    data: {
      id_awvisualmerchandising: id_awvisualmerchandising,
    },
    success: function (data) {
      loadingIndicator.hide();
      isLoading = false;

      if (data.success === false) {
        loadTrigger.hide();
        observer.disconnect();
      } else {
        data.ProductsList.forEach(function (product) {
          const productCard = $(
            '<div class="product-container">' +
              '<div class="product-card" data-id="' +
              product.id_product +
              '">' +
              '<img src="' +
              product.image +
              '" alt="' +
              product.name +
              '">' +
              '<div class="product-details">' +
              "<p><strong>" +
              product.name +
              "</strong></p>" +
              '<p><label class="plabel"> Ref </label>: ' +
              product.sku +
              "</p>" +
              '<p><label class="plabel"> SKU </label>: ' +
              product.ean13 +
              "</p>" +
              '<p><label class="plabel"> Brand </label>: ' +
              product.manufacturer_name +
              "</p>" +
              "</div>" +
              '<div class="overlay">' +
              '<button class="pin-button" data-id="' +
              product.id_product +
              '">' +
              '<img src="' +
              pathUri +
              '/views/img/pin.svg" alt="Pin" style="width: 20px; height: 20px; margin-right: 10px;">' +
              " Pin " +
              "</button>" +
              '<button class="hide-button" data-id="' +
              product.id_product +
              '">' +
              '<img src="' +
              pathUri +
              '/views/img/hide.svg" alt="Hide" style="width: 20px; height: 20px; margin-right: 10px;">' +
              " Hide " +
              "</button>" +
              "</div>" +
              "</div>" +
              "</div>"
          );
          productContainer.append(productCard);
        });
        page++;
        updateUIProducts();
        // Ensure the observer continues observing the trigger if it is in the DOM
        if (!document.body.contains(loadTrigger[0])) {
          observer.observe(loadTrigger[0]);
        }
      }
      updateUIProducts();
    },
    error: function (error) {
      loadingIndicator.hide();
      isLoading = false;
      console.error("Error loading more products:", error);
    },
  });
}

function initSelect2() {
  $(".select2").select2({
    placeholder: {
      id: "-1",
      text: select_an_option,
    },
    width: "100%",
  });
}

$(document).ready(function () {
  // Assign DOM elements
  productContainer = $("#product_list");
  loadTrigger = $("#load-more-trigger");
  loadingIndicator = $("#loading");
  rulesSoratable();
  // Initialize Intersection Observer for infinite scrolling
  observer = new IntersectionObserver(
    function (entries) {
      if (entries[0].isIntersecting && !isLoading) {
        loadMoreProducts();
      }
    },
    {
      root: null,
      threshold: 1.0,
    }
  );
  observer.observe(loadTrigger[0]);
  // handle blending display based on rule type
  $(".awblending").hide();
  $(document).on("change", "#ruleType", function () {
    let selectedValue = $(this).val();
    if (selectedValue === "boost") {
      $(".awblending").show();
    } else {
      $(".awblending").hide();
    }
  });

  $(document).on("change", "#edit_ruleType", function () {
    let selectedValue = $(this).val();
    if (selectedValue === "boost") {
      $(".edit_awblending").show();
    } else {
      $(".edit_awblending").hide();
    }
  });

  // rule segment logic
  $(".segment").hide();
  $(document).on("change", "#ruleSegment", function () {
    let seg = $(this).val();
    $(".segment").hide();
    if (seg) {
      $(".segment-" + seg).show();
    }
  });

  $(document).on("change", "#edit_ruleSegment", function () {
    let seg = $(this).val();
    $("#editRuleModal .segment").hide();
    if (seg) {
      $("#editRuleModal .segment-" + seg).show();
    }
  });

  $("#addRuleModal").on("show.bs.modal", function () {
    $(this).find(".segment").hide();
    $("#ruleSegment").val("").trigger("change");
  });

  $("#editRuleModal").on("shown.bs.modal", function () {
    $("#edit_ruleSegment").trigger("change");
  });

  //create a status toggle for publish button
  $("#save-status").on("click", function () {
    const status = $("#publish-status").val(); //0 or 1
    $.ajax({
      url:
        "index.php?controller=AdminMerchandising&ajax=1&action=updatePublishStatus&token=" +
        token,
      method: "POST",
      dataType: "json",
      data: {
        status: status,
        id_awvisualmerchandising: id_awvisualmerchandising,
      },
      success: function (data) {
        if (data.success) {
          showSuccessMessage(Status_updated_successfully);
        } else {
          showErrorMessage(Failed_to_update_status);
        }
      },
      error: function () {
        showErrorMessage(AJAX_request_failed);
      },
    });
  });

  $("#search-query").on("input", function () {
    const query = $(this).val().trim();
    if (query.length < 2) {
      $("#search-results").empty();
      return;
    }
    $.ajax({
      url: searchActionUrl,
      method: "POST",
      data: {
        query: query,
        id_awvisualmerchandising: id_awvisualmerchandising,
      },
      success: function (data) {
        let products;
        try {
          products = JSON.parse(data);
        } catch (e) {
          console.error("Invalid JSON response:", data);
          return;
        }

        $("#search-results").empty();
        if (products.length) {
          products.forEach(function (product) {
            $("#search-results").append(
              '<li class="search-product-item" data-id="' +
                product.id_product +
                '">' +
                '<div class="product-row">' +
                '<div class="product-image">' +
                '<img src="' +
                product.image +
                '" alt="' +
                product.name +
                '">' +
                "</div>" +
                '<div class="product-details">' +
                "<strong>" +
                product.name +
                "</strong>" +
                "<p>SKU: " +
                product.sku +
                ", EAN: " +
                product.ean13 +
                "</p>" +
                "</div>" +
                '<button class="btn btn-success add-to-pinned" data-id="' +
                product.id_product +
                '">' +
                "Add to Pinned Products" +
                "</button>" +
                "</div>" +
                "</li>"
            );
          });
        } else {
          $("#search-results").html("<li>" + No_products_found + "</li>");
        }
      },
      error: function () {
        console.error("An error occurred while searching.");
      },
    });
  });

  // Remove pinned product using SweetAlert2 confirmation
  $(document).on("click", ".remove-pinned-product", function () {
    const idPinnedProduct = $(this).data("id");
    const $productItem = $(this).closest(".pinned-product-item");

    Swal.fire({
      title: are_you_sure,
      text: Are_you_sure_you_want_to_remove_this_pinned_product,
      icon: warning,
      showCancelButton: true,
      confirmButtonText: Yes_remove_it,
      cancelButtonText: Cancel,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url:
            "index.php?controller=AdminMerchandising&ajax=1&action=RemovePinnedProduct&token=" +
            token,
          method: "POST",
          data: {
            id_pinned_product: idPinnedProduct,
            id_awvisualmerchandising: id_awvisualmerchandising,
          },
          success: function (response) {
            try {
              const data = JSON.parse(response);
              if (data.success) {
                $productItem.fadeOut(300, function () {
                  $(this).remove();
                });
                reloadListProducts();
                showSuccessMessage(data.message);
              } else {
                showErrorMessage(data.message);
              }
            } catch (e) {
              console.error("Invalid response:", response);
            }
          },
          error: function () {
            showErrorMessage(
              An_error_occurred_while_removing_the_pinned_product
            );
          },
        });
      }
    });
  });

  //end of function
  // Category select change event
  $("#categorySelect").change(function () {
    let categoryId = $(this).val();

    loadTrigger.show();
    observer.observe(loadTrigger[0]);
    $.ajax({
      type: "POST",
      url:
        "index.php?controller=AdminMerchandising&ajax=1&action=updateCategory&token=" +
        token,
      data: {
        category_id: categoryId,
        id_awvisualmerchandising: id_awvisualmerchandising,
      },
      success: function (response) {
        reloadListProducts();
        loadPinnedProducts();
        showSuccessMessage(Category_updated_successfully);
      },
    });
  });

  // Pin button click event
  $(document).on("click", ".pin-button", function () {
    let productId = $(this).data("id");
    let productCard = $(this).closest(".product-card");

    $.ajax({
      url:
        "index.php?controller=AdminMerchandising&ajax=1&action=addPinnedProduct&token=" +
        token,
      method: "POST",
      dataType: "json",
      data: {
        id_product: productId,
        id_awvisualmerchandising: id_awvisualmerchandising,
      },
      success: function (response) {
        if (response.success) {
          productCard.fadeOut(300, function () {
            $(this).remove();
          });
          loadPinnedProducts();
          reloadListProducts();
          showSuccessMessage(Product_pinned_successfully);
        } else {
          showErrorMessage(Failed_to_pin_the_product);
        }
      },
      error: function (error) {
        console.error("Error pinning the product:", error);
      },
    });
  });

  // Pin button click event
  $(document).on("click", ".hide-button", function () {
    let productId = $(this).data("id");
    let productCard = $(this).closest(".product-card");

    $.ajax({
      url:
        "index.php?controller=AdminMerchandising&ajax=1&action=hideProduct&token=" +
        token,
      method: "POST",
      dataType: "json",
      data: {
        id_product: productId,
        id_awvisualmerchandising: id_awvisualmerchandising,
      },
      success: function (response) {
        if (response.success) {
          productCard.fadeOut(300, function () {
            $(this).remove();
          });
          loadPinnedProducts();
          reloadListProducts();
          showSuccessMessage(Product_hidden_successfully);
        } else {
          showErrorMessage(Failed_to_hide_the_product);
        }
      },
      error: function (error) {
        console.error("Error pinning the product:", error);
      },
    });
  });

  // Initialize sortable for added rules
  //$('#addedRules').sortable();

  // Show modal on Add Rule button click
  $("#addRule").on("click", function () {
    $("#addRuleModal").modal("show");
  });

  $("#addRuleModal").on("show.bs.modal", function () {
    // Reset all select elements
    $(this)
      .find("select")
      .each(function () {
        $(this).val("");
        // Trigger change if needed for select2
        $(this).trigger("change");
      });
    // Uncheck all checkboxes
    $(this).find("input:checkbox").prop("checked", false);
  });
  // Save rule and add to list
  $("#saveRule").on("click", function () {
    let ruleType = $("#ruleType").val();
    if (!ruleType) {
      Swal.fire({
        icon: "warning",
        title: "Select Rule Type",
        text: "Please select a rule type",
      });
      return;
    }
    let ruleId = $("#ruleId").val();
    let ruleSegment = $("#ruleSegment").val();
    let attributeValues = {};
    let features = {};
    let brand = $("#brand").val();
    let supplier = $("#supplier").val();
    let newestRule = $("#newestRule").is(":checked") ? 1 : 0;
    let discountedRule = $("#discountedRule").is(":checked") ? 1 : 0;
    let lowstock = $("#lowStockRule").is(":checked") ? 1 : 0;

    if (ruleSegment === "attribute") {
      let count = 0;
      $(".attribute-select").each(function () {
        if ($(this).val()) {
          count++;
        }
      });
      if (count > 1) {
        Swal.fire({ icon: "warning", title: "Only one attribute allowed" });
        return;
      }
    }
    if (ruleSegment === "feature") {
      let count = 0;
      $('select[name^="features"]').each(function () {
        if ($(this).val() && $(this).val().length) {
          count++;
        }
      });
      if (count > 1) {
        Swal.fire({ icon: "warning", title: "Only one feature allowed" });
        return;
      }
    }

    // Collect attribute values
    $(".attribute-select").each(function () {
      let group = $(this).data("group");
      let selectedValue = $(this).val();
      if (selectedValue) {
        if (!attributeValues[group]) {
          attributeValues[group] = [];
        }
        attributeValues[group].push(selectedValue);
      }
    });

    // Collect feature values
    $('select[name^="features"]').each(function () {
      let nameAttr = $(this).attr("name");
      let match = nameAttr.match(/features\[(\d+)\]\[\]/);
      if (match) {
        let featureGroup = match[1];
        let selectedValues = $(this).val();
        if (selectedValues && selectedValues.length) {
          features[featureGroup] = selectedValues;
        }
      }
    });

    // get range values for attributes
    let rangeValues = {};
    $('input[name^="range["]').each(function () {
      let nameAttr = $(this).attr("name");
      let match = nameAttr.match(/range\[(.+?)\]/);
      if (match) {
        let group = match[1];
        rangeValues[group] = $(this).val();
      }
    });

    // get range values for features
    let rangeFeatureValues = {};
    $('input[name^="range_feature["]').each(function () {
      let nameAttr = $(this).attr("name");
      let match = nameAttr.match(/range_feature\[(.+?)\]/);
      if (match) {
        let featureId = match[1];
        rangeFeatureValues[featureId] = $(this).val();
      }
    });

    let brandRange = $("#range_brand").val();
    let supplierRange = $("#range_supplier").val();
    let newestRange = $("#range_newest").val();
    let discountedRange = $("#range_discounted").val();
    let lowstockRange = $("#range_lowstock").val();

    $.ajax({
      url:
        "index.php?controller=AdminMerchandising&ajax=1&action=SaveRule&token=" +
        token,
      method: "POST",
      data: {
        ruleId: ruleId,
        id_awvisualmerchandising: id_awvisualmerchandising,
        ruleType: ruleType,
        ruleSegment: ruleSegment,
        attributeValues: attributeValues,
        features: features,
        brand: brand,
        supplier: supplier,
        newestRule: newestRule,
        lowstock: lowstock,
        discountedRule: discountedRule,
        range: rangeValues,
        range_feature: rangeFeatureValues,
        brand_range: brandRange,
        supplier_range: supplierRange,
        newest_range: newestRange,
        discounted_range: discountedRange,
        lowstock_range: lowstockRange,
      },

      success: function (response) {
        try {
          const data = JSON.parse(response);
          if (data.success) {
            reloadRules();
            reloadListProducts();
            $("#addRuleModal").modal("hide");
          } else {
            showErrorMessage(Failed_to_save_rule_Please_try_again);
          }
        } catch (e) {
          console.error("Invalid JSON response", e);
          showErrorMessage(An_error_occurred_Please_try_again);
        }
      },
      error: function () {
        showErrorMessage(AJAX_request_failed);
      },
    });
  });

  // Update rule and add to list
  $("#updateRule").on("click", function () {
    let ruleType = $("#edit_ruleType").val();
    let ruleId = $("#edit_rule_id").val();
    let ruleSegment = $("#edit_ruleSegment").val();
    let attributeValues = {};
    let features = {};
    let brand = $("#edit_brand").val();
    let supplier = $("#edit_supplier").val();
    let newestRule = $("#edit_newestRule").is(":checked") ? 1 : 0;
    let discountedRule = $("#edit_discountedRule").is(":checked") ? 1 : 0;
    let lowstock = $("#edit_lowStockRule").is(":checked") ? 1 : 0;

    if (ruleSegment === "attribute") {
      let count = 0;
      $(".edit_attribute-select").each(function () {
        if ($(this).val()) {
          count++;
        }
      });
      if (count > 1) {
        Swal.fire({ icon: "warning", title: "Only one attribute allowed" });
        return;
      }
    }
    if (ruleSegment === "feature") {
      let count = 0;
      $('select[name^="edit_features"]').each(function () {
        if ($(this).val() && $(this).val().length) {
          count++;
        }
      });
      if (count > 1) {
        Swal.fire({ icon: "warning", title: "Only one feature allowed" });
        return;
      }
    }

    // Collect attribute values
    $(".edit_attribute-select").each(function () {
      let group = $(this).data("group");
      let selectedValue = $(this).val();
      if (selectedValue) {
        if (!attributeValues[group]) {
          attributeValues[group] = [];
        }
        attributeValues[group].push(selectedValue);
      }
    });

    // Collect feature values
    $('select[name^="edit_features"]').each(function () {
      let nameAttr = $(this).attr("name");
      let match = nameAttr.match(/edit_features\[(\d+)\]\[\]/);
      if (match) {
        let featureGroup = match[1];
        let selectedValues = $(this).val();
        if (selectedValues && selectedValues.length) {
          features[featureGroup] = selectedValues;
        }
      }
    });

    // get range values for attributes
    let rangeValues = {};
    $('input[name^="edit_range["]').each(function () {
      let nameAttr = $(this).attr("name");
      let match = nameAttr.match(/range\[(.+?)\]/);
      if (match) {
        let group = match[1];
        rangeValues[group] = $(this).val();
      }
    });

    // get range values for features
    let rangeFeatureValues = {};
    $('input[name^="edit_range_feature["]').each(function () {
      let nameAttr = $(this).attr("name");
      let match = nameAttr.match(/range_feature\[(.+?)\]/);
      if (match) {
        let featureId = match[1];
        rangeFeatureValues[featureId] = $(this).val();
      }
    });

    let brandRange = $("#range_brand").val();
    let supplierRange = $("#range_supplier").val();
    let newestRange = $("#range_newest").val();
    let discountedRange = $("#range_discounted").val();
    let lowstockRange = $("#range_lowstock").val();

    $.ajax({
      url:
        "index.php?controller=AdminMerchandising&ajax=1&action=updateRule&token=" +
        token,
      method: "POST",
      data: {
        ruleId: ruleId,
        id_awvisualmerchandising: id_awvisualmerchandising,
        ruleType: ruleType,
        ruleSegment: ruleSegment,
        attributeValues: attributeValues,
        features: features,
        brand: brand,
        supplier: supplier,
        newestRule: newestRule,
        discountedRule: discountedRule,
        lowstock: lowstock,
        range: rangeValues,
        range_feature: rangeFeatureValues,
        brand_range: brandRange,
        supplier_range: supplierRange,
        newest_range: newestRange,
        discounted_range: discountedRange,
        lowstock_range: lowstockRange,
      },
      success: function (response) {
        try {
          const data = JSON.parse(response);
          if (data.success) {
            reloadRules();
            reloadListProducts();
            $("#editRuleModal").modal("hide");
          } else {
            showErrorMessage(Failed_to_save_rule_Please_try_again);
          }
        } catch (e) {
          console.error("Invalid JSON response", e);
          showErrorMessage(An_error_occurred_Please_try_again);
        }
      },
      error: function () {
        showErrorMessage(AJAX_request_failed);
      },
    });
  });

  // Update desktop and mobile item rows
  $("#desktopItems, #mobileItems").on("change", function () {
    let desktopItems = $("#desktopItems").val();
    let mobileItems = $("#mobileItems").val();

    $.ajax({
      url:
        "index.php?controller=AdminMerchandising&ajax=1&action=updateRows&token=" +
        token,
      method: "POST",
      data: {
        id_awvisualmerchandising: id_awvisualmerchandising,
        desktop_rows: desktopItems,
        mobile_rows: mobileItems,
      },
      success: function (response) {
        try {
          const data = JSON.parse(response);
          if (data.success) {
            updateUIProducts();
            showSuccessMessage(Updated_successfully);
          } else {
            showErrorMessage(Failed_to_update);
          }
        } catch (e) {
          console.error("Invalid JSON response", e);
          showErrorMessage(An_error_occurred_while_updating);
        }
      },
      error: function () {
        showErrorMessage(Error_in_AJAX_request);
      },
    });
  });

  // Edit rule list item click event (for inline editing)
  $(document).on("click", ".edit-rule", function () {
    let ruleId = $(this).data("rule-id");
    $.ajax({
      url: "index.php?controller=AdminMerchandising&ajax=1&token=" + token,
      type: "POST",
      data: {
        action: loadEditModal,
        ruleId: ruleId,
        id_awvisualmerchandising: id_awvisualmerchandising,
      },
      success: function (response) {
        $(".edit-rule-modal-body").html(response);
        initAllRangeSliders();
        initSelect2();
        $("#editRuleModal").modal("show");
        $("#edit_ruleSegment").trigger("change");
      },
      error: function () {
        Swal.fire({
          icon: error,
          title: Error,
          text: Error_loading_edit_modal,
        });
      },
    });
  });

  // Delete rule click event using SweetAlert2 confirmation
  $(document).on("click", ".delete-rule", function () {
    let ruleId = $(this).data("rule-id");
    let ruleItem = $(this).closest("li");

    Swal.fire({
      title: are_you_sure,
      text: Are_you_sure_you_want_to_delete_this_rule,
      icon: warning,
      showCancelButton: true,
      confirmButtonText: Yes_delete_it,
      cancelButtonText: Cancel,
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url:
            "index.php?controller=AdminMerchandising&ajax=1&action=deleteRule&token=" +
            token,
          method: "POST",
          data: {
            ruleId: ruleId,
            id_awvisualmerchandising: id_awvisualmerchandising,
          },
          success: function (response) {
            try {
              let data = JSON.parse(response);
              if (data.success) {
                ruleItem.fadeOut(300, function () {
                  $(this).remove();
                });
                reloadRules();
                reloadListProducts();
                showSuccessMessage(Rule_deleted_successfully);
              } else {
                showErrorMessage(Failed_to_delete_rule);
              }
            } catch (e) {
              console.error("Invalid JSON response", e);
              showErrorMessage(An_error_occurred_while_processing_the_response);
            }
          },
          error: function () {
            showErrorMessage(AJAX_request_failed);
          },
        });
      }
    });
  });

  // Handle pin/unpin functionality
  $(document).on("click", ".toggle-pin", function () {
    const $icon = $(this);
    const productId = $icon.data("id");
    const $productCard = $icon.closest(".product-card");

    $.ajax({
      url:
        "index.php?controller=AdminMerchandising&ajax=1&action=RemovePinnedProduct&token=" +
        token,
      method: "POST",
      data: {
        id_product: productId,
        id_awvisualmerchandising: id_awvisualmerchandising,
      },
      success: function (response) {
        try {
          const data = JSON.parse(response);
          if (data.success) {
            $productCard.fadeOut(300, function () {
              $(this).remove();
            });
            showSuccessMessage(Product_unpinned_and_removed_from_the_list);
          } else {
            showErrorMessage(Failed_to_unpin_the_product);
          }
          // Refresh after removing
          loadPinnedProducts();
          reloadListProducts();
        } catch (e) {
          console.error("Invalid response:", response);
        }
      },
      error: function () {
        showErrorMessage(An_error_occurred_while_unpinning_the_product);
      },
    });
  });

  $(document).on("click", ".hide-pin", function () {
    const $icon = $(this);
    const productId = $icon.data("id");
    const $productCard = $icon.closest(".product-card");

    $.ajax({
      url:
        "index.php?controller=AdminMerchandising&ajax=1&action=RemoveHiddenProduct&token=" +
        token,
      method: "POST",
      data: {
        id_product: productId,
        id_awvisualmerchandising: id_awvisualmerchandising,
      },
      success: function (response) {
        try {
          const data = JSON.parse(response);
          if (data.success) {
            $productCard.fadeOut(300, function () {
              $(this).remove();
            });
            showSuccessMessage(Product_unhidden_and_removed_from_the_list);
          } else {
            showErrorMessage(Failed_to_unpin_the_product);
          }
          // Refresh after removing
          loadPinnedProducts();
          reloadListProducts();
        } catch (e) {
          console.error("Invalid response:", response);
        }
      },
      error: function () {
        showErrorMessage(An_error_occurred_while_unpinning_the_product);
      },
    });
  });

  // Add product to pinned products
  $(document).on("click", ".add-to-pinned", function () {
    const productId = $(this).data("id");
    const $productItem = $(this).closest(".search-product-item");

    $.ajax({
      url:
        "index.php?controller=AdminMerchandising&ajax=1&action=addPinnedProduct&token=" +
        token,
      method: "POST",
      data: {
        id_product: productId,
        id_awvisualmerchandising: id_awvisualmerchandising,
      },
      success: function (response) {
        try {
          const data = JSON.parse(response);
          if (data.success) {
            $productItem.fadeOut(300, function () {
              $(this).remove();
            });
            loadPinnedProducts();
            reloadListProducts();
            showSuccessMessage(Product_pinned_successfully);
          } else {
            showErrorMessage(Failed_to_pin_the_product);
          }
        } catch (e) {
          console.error("Invalid response:", response);
        }
      },
      error: function () {
        showErrorMessage(An_error_occurred_while_pinning_the_product);
      },
    });
  });

  // Helper function to initialize select2 on given selectors

  // Initialize select2 on page load
  initSelect2();

  // Reapply select2 for dynamically loaded content after every AJAX request completes
  $(document).ajaxComplete(function () {
    initSelect2();
  });
  loadPinnedProducts();
});

function updateUIProducts() {
  let vmobileItems;
  let vdesktopItems;
  vmobileItems = parseInt($("#mobileItems").val(), 10) || mobileItems;
  vdesktopItems = parseInt($("#desktopItems").val(), 10) || desktopItems;

  if (vmobileItems === 0 && vdesktopItems === 0) {
    vmobileItems = 2;
    vdesktopItems = 3;
  }
  let mobileColSpan = Math.floor(12 / vmobileItems);
  let desktopColSpan = Math.floor(12 / vdesktopItems);

  let colClasses = "col-xs-" + mobileColSpan + " col-md-" + desktopColSpan;

  $(".product-container").each(function () {
    $(this)
      .removeClass(function (index, className) {
        return (className.match(/(^|\s)col-\S+/g) || []).join(" ");
      })
      .addClass(colClasses);
  });
}

function initPinnedSortable() {
  const $pinnedProducts = $("#pinned-products");
  $pinnedProducts.sortable({
    items: ".product-card:not(.not-sortable)",
    cursor: "move",
    placeholder: "ui-state-highlight",
    stop: () => {
      const positions = $pinnedProducts
        .find(".product-card")
        .map((index, el) => $(el).data("id"))
        .get();

      $.ajax({
        url: `index.php?controller=AdminMerchandising&ajax=1&action=UpdatePinnedProductPosition&token=${token}`,
        type: "POST",
        data: {
          positions: positions,
          id_awvisualmerchandising: id_awvisualmerchandising,
        },
      })
        .done((response) => {
          let data;
          try {
            data = JSON.parse(response);
          } catch (err) {
            console.error("Failed to parse response:", err, response);
            showErrorMessage(An_error_occurred_while_processing_the_response);
            return;
          }
          if (data.success) {
            loadPinnedProducts();
            showSuccessMessage(Positions_updated_successfully);
          } else {
            showErrorMessage(Failed_to_update_positions);
          }
        })
        .fail((xhr, status, error) => {
          console.error("AJAX request failed:", status, error);
          showErrorMessage(An_error_occurred_while_updating_positions);
        });
    },
  });
}

//addedRules sortable
function rulesSoratable() {
  $("#__addedRules").sortable({
    items: "li", // assuming each rule is in an <li> element with data-rule-id attribute
    cursor: "move",
    //placeholder: "ui-state-highlight",
    update: function () {
      // Gather the new sort order (list of rule IDs)
      let sortedIds = $("#addedRules")
        .children("li")
        .map(function () {
          return $(this).data("rule-id");
        })
        .get();

      // Send the sorted list to the server to save the new sort order
      $.ajax({
        url:
          "index.php?controller=AdminMerchandising&ajax=1&action=updateRuleSort&token=" +
          token,
        method: "POST",
        data: {
          sortedIds: sortedIds,
          id_awvisualmerchandising: id_awvisualmerchandising,
        },
        success: function (response) {
          try {
            const data = JSON.parse(response);
            if (data.success) {
              showSuccessMessage(Rules_sorted_successfully);
              reloadRules();
              reloadListProducts();
            } else {
              showErrorMessage(Failed_to_update_rule_sort_order);
            }
          } catch (e) {
            console.error("Invalid JSON response", e);
            showErrorMessage(An_error_occurred_while_processing_the_response);
          }
        },
        error: function () {
          showErrorMessage(AJAX_request_failed);
        },
      });
    },
  });
}
function initAllRangeSliders() {
  // Unbind previous to avoid duplicate handlers
  $(".slider")
    .off("input")
    .on("input", function () {
      var $input = $(this);
      var id = $input.attr("id");

      // For attribute range sliders
      if (id.startsWith("range_") && !id.startsWith("range_feature_")) {
        var group = id.replace("range_", "");
        if (group === "brand") {
          $("#edit_rangeBrandOutput").text($input.val() + "%");
        } else if (group === "supplier") {
          $("#edit_rangeSupplierOutput").text($input.val() + "%");
        } else if (group === "newest") {
          $("#edit_rangeNewestOutput").text($input.val() + "%");
        } else if (group === "discounted") {
          $("#edit_rangeDiscountedOutput").text($input.val() + "%");
        } else if (group === "lowstock") {
          $("#edit_rangeLowstockOutput").text($input.val() + "%");
        } else {
          $("#edit_rangeOutput_" + group).text($input.val() + "%");
        }
      }

      // For feature range sliders
      if (id.startsWith("range_feature_")) {
        var featureId = id.replace("range_feature_", "");
        $("#edit_rangeFeatureOutput_" + featureId).text($input.val() + "%");
      }
    });
}

  function rulesSortable() {
    $(".aw-visual-list tbody").sortable({
      handle: ".drag-handle",
      update: function () {
        console.log("Items reordered.");
      }
    });
  }
////////////////////////////////////////////////

$(document).ready(function () {
  $(document).on("click", ".list-action-enable", function (e) {
    e.preventDefault(); // 👈 Yeh stop karega href follow hone se
    e.stopImmediatePropagation();

    const el = $(this);
    const href = el.attr("href");

    const params = new URLSearchParams(href.split("?")[1]);
    const id = params.get("id_awvisualmerchandising");
    const token = params.get("token");
    const status = el.hasClass("action-enabled") ? 0 : 1;

    // AJAX call
    $.post(`index.php?controller=AdminMerchandising&ajax=1&action=updatePublishStatus&token=${token}`, {
      id_awvisualmerchandising: id,
      status: status
    }).done(function (res) {
      if (res.success) {
        el.toggleClass("action-enabled", status === 1);
        el.toggleClass("action-disabled", status === 0);
        el.attr("title", status ? "Enabled" : "Disabled");
        el.find(".icon-check").toggleClass("hidden", status === 0);
        el.find(".icon-remove").toggleClass("hidden", status === 1);
      } else {
        alert("Status update failed.");
      }
    });

    return false;
  });
});

