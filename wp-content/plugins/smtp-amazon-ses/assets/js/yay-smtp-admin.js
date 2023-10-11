var yay_smtp_amazonses_prefix = "yay_smtp_amazonses";
var yay_smtp_amazonses_class_obj = ".yay_smtp_amazonses";
(function($) {
  $(document).ready(function() {
    // Catch Pagination per page event
    $(
      yay_smtp_amazonses_class_obj +
        ".yay-smtp-wrap.mail-logs .pag-per-page-sel"
    ).change(function() {
      let param = searchYaySmtpAmazonSESConditionBasicCurrent();
      param.sortField = yay_smtp_amazonses_sort_field;
      param.sortVal = yay_smtp_amazonses_sort_val;
      YaySmtpAmazonSESEmailLogsList(param);
    });

    // Catch current page event
    $(
      yay_smtp_amazonses_class_obj +
        ".yay-smtp-wrap.mail-logs .pag-page-current"
    ).change(function() {
      let param = searchYaySmtpAmazonSESConditionBasicCurrent();
      param.sortField = yay_smtp_amazonses_sort_field;
      param.sortVal = yay_smtp_amazonses_sort_val;
      YaySmtpAmazonSESEmailLogsList(param);
    });

    // Catch previous page event
    $(
      yay_smtp_amazonses_class_obj +
        ".yay-smtp-wrap.mail-logs .pagination-link.previous-btn"
    ).click(function() {
      let limit = $(
        yay_smtp_amazonses_class_obj +
          ".yay-smtp-wrap.mail-logs .pag-per-page-sel"
      ).val();
      let page = $(
        yay_smtp_amazonses_class_obj +
          ".yay-smtp-wrap.mail-logs .pag-page-current"
      ).val();
      let valSearch = $(
        yay_smtp_amazonses_class_obj +
          ".yay-smtp-wrap.mail-logs .yay-button-wrap .search .search-imput"
      ).val();
      let status;
      if (
        $(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_status_not_send").is(
          ":checked"
        )
      ) {
        if (
          $(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_status_sent").is(
            ":checked"
          )
        ) {
          status = "all";
        } else {
          status = "not_send";
        }
      } else {
        if (
          $(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_status_sent").is(
            ":checked"
          )
        ) {
          status = "sent";
        } else {
          status = "empty";
        }
      }
      let param = {
        page: parseInt(page) - 1,
        limit: parseInt(limit),
        valSearch: valSearch,
        status: status
      };
      param.sortField = yay_smtp_amazonses_sort_field;
      param.sortVal = yay_smtp_amazonses_sort_val;
      YaySmtpAmazonSESEmailLogsList(param);
    });

    // Catch next page event
    $(
      yay_smtp_amazonses_class_obj +
        ".yay-smtp-wrap.mail-logs .pagination-link.next-btn"
    ).click(function() {
      let limit = $(
        yay_smtp_amazonses_class_obj +
          ".yay-smtp-wrap.mail-logs .pag-per-page-sel"
      ).val();
      let page = $(
        yay_smtp_amazonses_class_obj +
          ".yay-smtp-wrap.mail-logs .pag-page-current"
      ).val();
      let valSearch = $(
        yay_smtp_amazonses_class_obj +
          ".yay-smtp-wrap.mail-logs .yay-button-wrap .search .search-imput"
      ).val();
      let status;
      if (
        $(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_status_not_send").is(
          ":checked"
        )
      ) {
        if (
          $(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_status_sent").is(
            ":checked"
          )
        ) {
          status = "all";
        } else {
          status = "not_send";
        }
      } else {
        if (
          $(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_status_sent").is(
            ":checked"
          )
        ) {
          status = "sent";
        } else {
          status = "empty";
        }
      }
      let param = {
        page: parseInt(page) + 1,
        limit: parseInt(limit),
        valSearch: valSearch,
        status: status
      };
      param.sortField = yay_smtp_amazonses_sort_field;
      param.sortVal = yay_smtp_amazonses_sort_val;
      YaySmtpAmazonSESEmailLogsList(param);
    });

    // Catch search even
    var yaysmtpAmazonSESTimeout = null;
    $(
      yay_smtp_amazonses_class_obj +
        ".yay-smtp-wrap.mail-logs .yay-button-wrap .search .search-imput"
    ).keyup(function(e) {
      clearTimeout(yaysmtpAmazonSESTimeout);
      yaysmtpAmazonSESTimeout = setTimeout(function() {
        let param = searchYaySmtpAmazonSESConditionBasicCurrent();
        param.sortField = yay_smtp_amazonses_sort_field;
        param.sortVal = yay_smtp_amazonses_sort_val;
        YaySmtpAmazonSESEmailLogsList(param);
      }, 1000);
    });

    // Catch components-dropdown-button even
    $(
      yay_smtp_amazonses_class_obj +
        ".yay-smtp-wrap.mail-logs .components-dropdown-button"
    ).click(function() {
      if ($(this).hasClass("is-opened")) {
        $(this).removeClass("is-opened");
        $(
          yay_smtp_amazonses_class_obj +
            ".yay-smtp-wrap.mail-logs .components-popover.components-dropdown__content"
        ).removeClass("is-opened");
      } else {
        $(this).addClass("is-opened");
        $(
          yay_smtp_amazonses_class_obj +
            ".yay-smtp-wrap.mail-logs .components-popover.components-dropdown__content"
        ).addClass("is-opened");
      }
    });

    // Catch yaysmtp_logs_subject_control column
    $(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_subject_control").click(
      function() {
        let show_subj_cl;
        if ($(this).is(":checked")) {
          $(
            yay_smtp_amazonses_class_obj +
              ".yay-smtp-wrap.mail-logs .yay-smtp-content .subject-col"
          ).show();
          show_subj_cl = 1;
        } else {
          $(
            yay_smtp_amazonses_class_obj +
              ".yay-smtp-wrap.mail-logs .yay-smtp-content .subject-col"
          ).hide();
          show_subj_cl = 0;
        }

        //Update DB
        $.ajax({
          url: yay_smtp_amazonses_wp_data.YAY_ADMIN_AJAX,
          type: "POST",
          // async: false,
          data: {
            action: yay_smtp_amazonses_prefix + "_set_email_logs_setting",
            nonce: yay_smtp_amazonses_wp_data.ajaxNonce,
            params: { show_subject_cl: show_subj_cl }
          },
          beforeSend: function() {},
          success: function(result) {}
        });
      }
    );

    // Catch yaysmtp_logs_to_control column
    $(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_to_control").click(
      function() {
        let show_to_cl;
        if ($(this).is(":checked")) {
          $(
            yay_smtp_amazonses_class_obj +
              ".yay-smtp-wrap.mail-logs .yay-smtp-content .to-col"
          ).show();
          show_to_cl = 1;
        } else {
          $(
            yay_smtp_amazonses_class_obj +
              ".yay-smtp-wrap.mail-logs .yay-smtp-content .to-col"
          ).hide();
          show_to_cl = 0;
        }
        //Update DB
        $.ajax({
          url: yay_smtp_amazonses_wp_data.YAY_ADMIN_AJAX,
          type: "POST",
          // async: false,
          data: {
            action: yay_smtp_amazonses_prefix + "_set_email_logs_setting",
            nonce: yay_smtp_amazonses_wp_data.ajaxNonce,
            params: { show_to_cl: show_to_cl }
          },
          beforeSend: function() {},
          success: function(result) {}
        });
      }
    );

    // Catch yaysmtp_logs_status_control column
    $(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_status_control").click(
      function() {
        let show_status_cl;
        if ($(this).is(":checked")) {
          $(
            yay_smtp_amazonses_class_obj +
              ".yay-smtp-wrap.mail-logs .yay-smtp-content .status-col"
          ).show();
          show_status_cl = 1;
        } else {
          $(
            yay_smtp_amazonses_class_obj +
              ".yay-smtp-wrap.mail-logs .yay-smtp-content .status-col"
          ).hide();
          show_status_cl = 0;
        }
        //Update DB
        $.ajax({
          url: yay_smtp_amazonses_wp_data.YAY_ADMIN_AJAX,
          type: "POST",
          // async: false,
          data: {
            action: yay_smtp_amazonses_prefix + "_set_email_logs_setting",
            nonce: yay_smtp_amazonses_wp_data.ajaxNonce,
            params: { show_status_cl: show_status_cl }
          },
          beforeSend: function() {},
          success: function(result) {}
        });
      }
    );

    // Catch yaysmtp_logs_datetime_control column
    $(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_datetime_control").click(
      function() {
        let show_datetime_cl;
        if ($(this).is(":checked")) {
          $(
            yay_smtp_amazonses_class_obj +
              ".yay-smtp-wrap.mail-logs .yay-smtp-content .datetime-col"
          ).show();
          show_datetime_cl = 1;
        } else {
          $(
            yay_smtp_amazonses_class_obj +
              ".yay-smtp-wrap.mail-logs .yay-smtp-content .datetime-col"
          ).hide();
          show_datetime_cl = 0;
        }
        //Update DB
        $.ajax({
          url: yay_smtp_amazonses_wp_data.YAY_ADMIN_AJAX,
          type: "POST",
          // async: false,
          data: {
            action: yay_smtp_amazonses_prefix + "_set_email_logs_setting",
            nonce: yay_smtp_amazonses_wp_data.ajaxNonce,
            params: { show_datetime_cl: show_datetime_cl }
          },
          beforeSend: function() {},
          success: function(result) {}
        });
      }
    );

    // Catch yaysmtp_logs_action_control column
    $(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_action_control").click(
      function() {
        let show_action_cl;
        if ($(this).is(":checked")) {
          $(
            yay_smtp_amazonses_class_obj +
              ".yay-smtp-wrap.mail-logs .yay-smtp-content .action-col"
          ).show();
          show_action_cl = 1;
        } else {
          $(
            yay_smtp_amazonses_class_obj +
              ".yay-smtp-wrap.mail-logs .yay-smtp-content .action-col"
          ).hide();
          show_action_cl = 0;
        }
        //Update DB
        $.ajax({
          url: yay_smtp_amazonses_wp_data.YAY_ADMIN_AJAX,
          type: "POST",
          // async: false,
          data: {
            action: yay_smtp_amazonses_prefix + "_set_email_logs_setting",
            nonce: yay_smtp_amazonses_wp_data.ajaxNonce,
            params: { show_action_cl: show_action_cl }
          },
          beforeSend: function() {},
          success: function(result) {}
        });
      }
    );

    // Catch sent control
    $(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_status_sent").click(
      function() {
        let status;
        if ($(this).is(":checked")) {
          if (
            $(
              yay_smtp_amazonses_class_obj + " #yaysmtp_logs_status_not_send"
            ).is(":checked")
          ) {
            status = "all";
          } else {
            status = "sent";
          }
        } else {
          if (
            $(
              yay_smtp_amazonses_class_obj + " #yaysmtp_logs_status_not_send"
            ).is(":checked")
          ) {
            status = "not_send";
          } else {
            status = "empty";
          }
        }

        //Update DB
        $.ajax({
          url: yay_smtp_amazonses_wp_data.YAY_ADMIN_AJAX,
          type: "POST",
          // async: false,
          data: {
            action: yay_smtp_amazonses_prefix + "_set_email_logs_setting",
            nonce: yay_smtp_amazonses_wp_data.ajaxNonce,
            params: { status: status }
          },
          beforeSend: function() {},
          success: function(result) {}
        });

        let limit = $(
          yay_smtp_amazonses_class_obj +
            ".yay-smtp-wrap.mail-logs .pag-per-page-sel"
        ).val();
        let page = $(
          yay_smtp_amazonses_class_obj +
            ".yay-smtp-wrap.mail-logs .pag-page-current"
        ).val();
        let valSearch = $(
          yay_smtp_amazonses_class_obj +
            ".yay-smtp-wrap.mail-logs .yay-button-wrap .search .search-imput"
        ).val();
        let param = {
          page: parseInt(page) - 1,
          limit: parseInt(limit),
          valSearch: valSearch,
          status: status
        };
        param.sortField = yay_smtp_amazonses_sort_field;
        param.sortVal = yay_smtp_amazonses_sort_val;
        YaySmtpAmazonSESEmailLogsList(param);
      }
    );

    // Catch not send control
    $(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_status_not_send").click(
      function() {
        let status;
        if ($(this).is(":checked")) {
          if (
            $(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_status_sent").is(
              ":checked"
            )
          ) {
            status = "all";
          } else {
            status = "not_send";
          }
        } else {
          if (
            $(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_status_sent").is(
              ":checked"
            )
          ) {
            status = "sent";
          } else {
            status = "empty";
          }
        }

        //Update DB
        $.ajax({
          url: yay_smtp_amazonses_wp_data.YAY_ADMIN_AJAX,
          type: "POST",
          // async: false,
          data: {
            action: yay_smtp_amazonses_prefix + "_set_email_logs_setting",
            nonce: yay_smtp_amazonses_wp_data.ajaxNonce,
            params: { status: status }
          },
          beforeSend: function() {},
          success: function(result) {}
        });

        let limit = $(
          yay_smtp_amazonses_class_obj +
            ".yay-smtp-wrap.mail-logs .pag-per-page-sel"
        ).val();
        let page = $(
          yay_smtp_amazonses_class_obj +
            ".yay-smtp-wrap.mail-logs .pag-page-current"
        ).val();
        let valSearch = $(
          yay_smtp_amazonses_class_obj +
            ".yay-smtp-wrap.mail-logs .yay-button-wrap .search .search-imput"
        ).val();
        let param = {
          page: parseInt(page),
          limit: parseInt(limit),
          valSearch: valSearch,
          status: status
        };
        param.sortField = yay_smtp_amazonses_sort_field;
        param.sortVal = yay_smtp_amazonses_sort_val;
        YaySmtpAmazonSESEmailLogsList(param);
      }
    );

    // Sorting - start
    var yay_smtp_amazonses_sort_field = "";
    var yay_smtp_amazonses_sort_val = "";
    $(
      yay_smtp_amazonses_class_obj + " .yay-smtp-content thead th.is-sortable"
    ).click(function() {
      let sortField = $(this).attr("data-sort-col");
      let sortVal = $(this).attr("data-sort");
      let sortValReal = "descending";
      if (sortVal == "descending") {
        sortValReal = "ascending";
        $(
          yay_smtp_amazonses_class_obj +
            " .yay-smtp-content .table-header button svg path"
        ).attr("d", "M6.5 12.4L12 8l5.5 4.4-.9 1.2L12 10l-4.5 3.6-1-1.2z");
      } else {
        $(
          yay_smtp_amazonses_class_obj +
            " .yay-smtp-content .table-header button svg path"
        ).attr("d", "M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z");
      }
      $(this).attr("data-sort", sortValReal);

      // Update global val for yay_smtp_amazonses_sort_field, yay_smtp_amazonses_sort_val
      yay_smtp_amazonses_sort_field = sortField;
      yay_smtp_amazonses_sort_val = sortValReal;

      let otherCol = $(
        yay_smtp_amazonses_class_obj + " .yay-smtp-content thead th.is-sortable"
      ).not(this);
      $.each(otherCol, function() {
        $(this).attr("data-sort", "none");
      });

      let param = searchYaySmtpAmazonSESConditionBasicCurrent();
      param.sortField = yay_smtp_amazonses_sort_field;
      param.sortVal = yay_smtp_amazonses_sort_val;

      YaySmtpAmazonSESEmailLogsList(param);
    });
    // Sorting -end

    // Delete item action
    $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs").on(
      "click",
      ".yaysmtp-delete-btn",
      function() {
        if (confirm("Are you sure to delete this item?")) {
          let idMailLog = $(this).attr("data-id");
          $.ajax({
            url: yay_smtp_amazonses_wp_data.YAY_ADMIN_AJAX,
            type: "POST",
            data: {
              action: yay_smtp_amazonses_prefix + "_delete_email_logs",
              nonce: yay_smtp_amazonses_wp_data.ajaxNonce,
              params: {
                ids: idMailLog
              }
            },
            beforeSend: function() {
              YaySmtpAmazonSESSpinner("yay-smtp-wrap", true);
            },
            success: function(result) {
              if (result.success) {
                let param = searchYaySmtpAmazonSESConditionBasicCurrent();
                param.sortField = yay_smtp_amazonses_sort_field;
                param.sortVal = yay_smtp_amazonses_sort_val;
                YaySmtpAmazonSESEmailLogsList(param);
              }
              YaySmtpAmazonSESNotification(result.data.mess, "yay-smtp-wrap");
              YaySmtpAmazonSESSpinner("yay-smtp-wrap", false);
            }
          });
        }
      }
    );

    // Delete item selected action
    $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs").on(
      "click",
      ".bulk-action-control .delete-selected-button",
      function() {
        let idMailLogs = [];
        let mailLogCheckeds = $(
          yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs"
        ).find(".checkbox-control-input-el:checked");

        mailLogCheckeds.each(function() {
          let id = $(this).val();
          idMailLogs.push(id);
        });

        if (idMailLogs.length > 0) {
          $.ajax({
            url: yay_smtp_amazonses_wp_data.YAY_ADMIN_AJAX,
            type: "POST",
            data: {
              action: yay_smtp_amazonses_prefix + "_delete_email_logs",
              nonce: yay_smtp_amazonses_wp_data.ajaxNonce,
              params: {
                ids: idMailLogs.join(",")
              }
            },
            beforeSend: function() {
              YaySmtpAmazonSESSpinner("yay-smtp-wrap", true);
            },
            success: function(result) {
              if (result.success) {
                let param = searchYaySmtpAmazonSESConditionBasicCurrent();
                param.sortField = yay_smtp_amazonses_sort_field;
                param.sortVal = yay_smtp_amazonses_sort_val;
                YaySmtpAmazonSESEmailLogsList(param);
              }
              YaySmtpAmazonSESNotification(result.data.mess, "yay-smtp-wrap");
              YaySmtpAmazonSESSpinner("yay-smtp-wrap", false);
            }
          });
        }
      }
    );

    // Delete all mail logs
    $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs").on(
      "click",
      ".yay-smtp-delete-all-mail-logs",
      function() {
        if (confirm("Are you sure to delete all mail logs?")) {
          $.ajax({
            url: yay_smtp_amazonses_wp_data.YAY_ADMIN_AJAX,
            type: "POST",
            data: {
              action: yay_smtp_amazonses_prefix + "_delete_all_email_logs",
              nonce: yay_smtp_amazonses_wp_data.ajaxNonce,
              params: {}
            },
            beforeSend: function() {
              YaySmtpAmazonSESSpinner("yay-smtp-wrap", true);
            },
            success: function(result) {
              if (result.success) {
                let param = searchYaySmtpAmazonSESConditionBasicCurrent();
                YaySmtpAmazonSESEmailLogsList(param);
              } 

              YaySmtpAmazonSESNotification(result.data.mess, "yay-smtp-wrap");
              YaySmtpAmazonSESSpinner("yay-smtp-wrap", false);
            }
          });
        }
      }
    );
    
    //Catch input-check-all event
    $(
      yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs #input-check-all"
    ).click(function() {
      if ($(this).is(":checked")) {
        $(
          yay_smtp_amazonses_class_obj +
            ".yay-smtp-wrap.mail-logs .checkbox-control-input-el"
        ).prop("checked", true);
        $(
          yay_smtp_amazonses_class_obj +
            ".yay-smtp-wrap.mail-logs .delete-selected-button"
        ).prop("disabled", false);
      } else {
        $(
          yay_smtp_amazonses_class_obj +
            ".yay-smtp-wrap.mail-logs .checkbox-control-input-el"
        ).prop("checked", false);
        $(
          yay_smtp_amazonses_class_obj +
            ".yay-smtp-wrap.mail-logs .delete-selected-button"
        ).prop("disabled", true);
      }
    });

    // Disable/Enable "Selected delete" button on ready page
    if (
      $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs").find(
        ".checkbox-control-input-el:checked"
      ).length > 0
    ) {
      $(
        yay_smtp_amazonses_class_obj +
          ".yay-smtp-wrap.mail-logs .delete-selected-button"
      ).prop("disabled", false);
    } else {
      $(
        yay_smtp_amazonses_class_obj +
          ".yay-smtp-wrap.mail-logs .delete-selected-button"
      ).prop("disabled", true);
    }

    //Catch checkbox-control-input-el element event
    $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs").on(
      "click",
      ".checkbox-control-input-el",
      function() {
        let mailLogsLength = $(
          yay_smtp_amazonses_class_obj +
            ".yay-smtp-wrap.mail-logs .checkbox-control-input-el"
        ).length;
        let mailLogsCheckedLength = $(
          yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs"
        ).find(".checkbox-control-input-el:checked").length;
        if (mailLogsCheckedLength > 0) {
          $(
            yay_smtp_amazonses_class_obj +
              ".yay-smtp-wrap.mail-logs .delete-selected-button"
          ).prop("disabled", false);
        } else {
          $(
            yay_smtp_amazonses_class_obj +
              ".yay-smtp-wrap.mail-logs .delete-selected-button"
          ).prop("disabled", true);
        }

        if ($(this).is(":checked")) {
          if (mailLogsCheckedLength == mailLogsLength) {
            $(
              yay_smtp_amazonses_class_obj +
                ".yay-smtp-wrap.mail-logs #input-check-all"
            ).prop("checked", true);
          } else {
            $(
              yay_smtp_amazonses_class_obj +
                ".yay-smtp-wrap.mail-logs #input-check-all"
            ).prop("checked", false);
          }
        } else {
          $(
            yay_smtp_amazonses_class_obj +
              ".yay-smtp-wrap.mail-logs #input-check-all"
          ).prop("checked", false);
        }
      }
    );

    // View Mail log icon click
    $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs").on(
      "click",
      ".yaysmtp-view-btn, td.subject-col a",
      function(e) {
        e.preventDefault();

        // Clean "is-active" class that no "this" elment.
        let otherCol = $(
          yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs"
        )
          .find(".yaysmtp-view-btn, td.subject-col a")
          .not(this);
        $.each(otherCol, function() {
          $(this).removeClass("is-active");
        });

        if ($(this).hasClass("is-active")) {
          // Close drawer
          $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
            .find(".yay-smtp-mail-detail-drawer")
            .css("width", "0");
          $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
            .find(".yay-smtp-mail-detail-drawer")
            .removeClass("is-open");
          $(this).removeClass("is-active");
        } else {
          // Open drawer
          /* Set the width of the side navigation to 35% */
          $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
            .find(".yay-smtp-mail-detail-drawer")
            .css("width", "65%");
          $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
            .find(".yay-smtp-mail-detail-drawer")
            .addClass("is-open");
          $(this).addClass("is-active");

          // Load email log detail
          let idEmailLog = $(this).attr("data-id");
          $.ajax({
            url: yay_smtp_amazonses_wp_data.YAY_ADMIN_AJAX,
            type: "POST",
            data: {
              action: yay_smtp_amazonses_prefix + "_detail_email_logs",
              nonce: yay_smtp_amazonses_wp_data.ajaxNonce,
              params: {
                id: idEmailLog
              }
            },
            beforeSend: function() {
              YaySmtpAmazonSESSpinner("yay-smtp-mail-detail-drawer", true);
            },
            success: function(result) {
              if (result.success) {
                let data = result.data.data;
                let status = "Success";
                let status_cl = "email-success";
                if (parseInt(data.status) == 0) {
                  status = "Fail";
                  status_cl = "email-fail";
                } else if (parseInt(data.status) == 2) {
                  status = "Waiting";
                  status_cl = "email-waiting";
                }

                let emailTo = data.email_to;
                $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
                  .find(
                    ".yay-smtp-mail-detail-drawer .yay-smtp-activity-panel-header-title"
                  )
                  .html("Email log #" + data.id);

                $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
                  .find(".yay-smtp-mail-detail-drawer .datetime-el .content")
                  .html(data.date_time);

                $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
                  .find(".yay-smtp-mail-detail-drawer .from-el .content")
                  .html(data.email_from);

                $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
                  .find(".yay-smtp-mail-detail-drawer .to-el .content")
                  .html(emailTo.toString());

                $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
                  .find(".yay-smtp-mail-detail-drawer .subject-el .content")
                  .html(data.subject);

                $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
                  .find(".yay-smtp-mail-detail-drawer .mailer-el .content")
                  .html(data.mailer);

                $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
                  .find(".yay-smtp-mail-detail-drawer .status-el .content")
                  .html(status);

                if (typeof data.reason_error !== "undefined") {
                  $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
                    .find(
                      ".yay-smtp-mail-detail-drawer .status-el .reason_error"
                    )
                    .html(data.reason_error);
                } else {
                  $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
                    .find(
                      ".yay-smtp-mail-detail-drawer .status-el .reason_error"
                    )
                    .html("");
                }

                if (typeof data.body_content !== "undefined") {
                  $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
                    .find(".yay-smtp-mail-detail-drawer .mail-body-el")
                    .show();
                  $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
                    .find(
                      ".yay-smtp-mail-detail-drawer .mail-body-content-detail"
                    )
                    .html(data.body_content);
                } else {
                  $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
                    .find(".yay-smtp-mail-detail-drawer .mail-body-el")
                    .hide();
                }

                $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
                  .find(".yay-smtp-mail-detail-drawer .status-el mark")
                  .removeClass();
                $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
                  .find(".yay-smtp-mail-detail-drawer .status-el mark")
                  .addClass("email-status")
                  .addClass(status_cl);
              }
              YaySmtpAmazonSESSpinner("yay-smtp-mail-detail-drawer", false);
            }
          });
        }
      }
    );

    // Close Mail log panel
    $(
      yay_smtp_amazonses_class_obj + " .yay-smtp-mail-detail-drawer .closebtn"
    ).click(function() {
      $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
        .find(".yay-smtp-mail-detail-drawer")
        .css("width", "0");
      $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
        .find(".yay-smtp-mail-detail-drawer")
        .removeClass("is-open");
      $(
        yay_smtp_amazonses_class_obj +
          ".yay-smtp-wrap.mail-logs .yaysmtp-view-btn"
      ).removeClass("is-active");
    });

    // Mail log settigns drawer
    $(
      yay_smtp_amazonses_class_obj +
        " .yay-smtp-button.yaysmtp-email-log-settings"
    ).click(function(e) {
      e.preventDefault();
      if ($(this).hasClass("is-active")) {
        // Close drawer
        $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
          .find(".yay-smtp-mail-log-settings-drawer")
          .css("width", "0");
        $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
          .find(".yay-smtp-mail-log-settings-drawer")
          .removeClass("is-open");
        $(this).removeClass("is-active");
      } else {
        // Open drawer
        /* Set the width of the side navigation to 35% */
        $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
          .find(".yay-smtp-mail-log-settings-drawer")
          .css("width", "30%");
        $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
          .find(".yay-smtp-mail-log-settings-drawer")
          .addClass("is-open");
        $(this).addClass("is-active");
      }
    });

    // Close Mail log panel
    $(
      yay_smtp_amazonses_class_obj +
        " .yay-smtp-mail-log-settings-drawer .closebtn"
    ).click(function() {
      $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
        .find(".yay-smtp-mail-log-settings-drawer")
        .css("width", "0");
      $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
        .find(".yay-smtp-mail-log-settings-drawer")
        .removeClass("is-open");

      $(
        yay_smtp_amazonses_class_obj +
          " .yay-smtp-button.yaysmtp-email-log-settings"
      ).removeClass("is-active");
    });

    // Save Email logs setting
    $(
      yay_smtp_amazonses_class_obj + " .yay-smtp-email-log-settings-save-action"
    ).click(function() {
      let logSettings = {};
      if (
        $(yay_smtp_amazonses_class_obj + " #yay_smtp_mail_log_setting_save").is(
          ":checked"
        )
      ) {
        logSettings["save_email_log"] = "yes";
      } else {
        logSettings["save_email_log"] = "no";
      }

      logSettings["email_log_inf_type"] = $(
        yay_smtp_amazonses_class_obj +
          '.yay-smtp-wrap.mail-logs input[name="information_type"]:checked'
      ).val();

      logSettings["email_log_delete_time"] = parseInt(
        $(
          yay_smtp_amazonses_class_obj +
            " .yay-smtp-email-log-setting-delete-time"
        ).val()
      );

      $.ajax({
        url: yay_smtp_amazonses_wp_data.YAY_ADMIN_AJAX,
        type: "POST",
        data: {
          action: yay_smtp_amazonses_prefix + "_set_email_logs_setting",
          nonce: yay_smtp_amazonses_wp_data.ajaxNonce,
          params: logSettings
        },
        beforeSend: function() {
          YaySmtpAmazonSESSpinner("yay-smtp-wrap", true);
        },
        success: function(result) {
          YaySmtpAmazonSESNotification(result.data.mess, "yay-smtp-wrap");
          YaySmtpAmazonSESSpinner("yay-smtp-wrap", false);
        }
      });
    });

    // Panel click
    $(yay_smtp_amazonses_class_obj + " .send-test-mail-panel").click(
      function() {
        if ($(this).hasClass("is-active")) {
          // Close drawer
          $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap")
            .find(".yay-smtp-test-mail-drawer")
            .css("width", "0");
          $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap")
            .find(".yay-smtp-test-mail-drawer")
            .removeClass("is-open");
          $(this).removeClass("is-active");
        } else {
          // Open drawer
          /* Set the width of the side navigation to 35% */
          $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap")
            .find(".yay-smtp-test-mail-drawer")
            .css("width", "35%");
          $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap")
            .find(".yay-smtp-test-mail-drawer")
            .addClass("is-open");
          $(this).addClass("is-active");
        }
      }
    );

    // Close panel
    $(
      yay_smtp_amazonses_class_obj + " .yay-smtp-test-mail-drawer .closebtn"
    ).click(function() {
      $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap")
        .find(".yay-smtp-test-mail-drawer")
        .css("width", "0");
      $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap")
        .find(".yay-smtp-test-mail-drawer")
        .removeClass("is-open");
      $(yay_smtp_amazonses_class_obj + " .send-test-mail-panel").removeClass(
        "is-active"
      );
    });

    //Close panel event when click outside the element
    $(document).mousedown(function(e) {
      // Close Test email drawer - start
      let container = $(
        yay_smtp_amazonses_class_obj + " .yay-smtp-test-mail-drawer"
      );
      let testMailPanel = $(
        yay_smtp_amazonses_class_obj + " .send-test-mail-panel"
      );
      let svgMail = $(yay_smtp_amazonses_class_obj + ' svg[data-icon="mail"]');
      let iconMailText = $(
        yay_smtp_amazonses_class_obj + " .send-test-mail-panel span.text"
      );
      // if the target of the click isn't the container nor a descendant of the container
      if (
        container.hasClass("is-open") &&
        testMailPanel.hasClass("is-active") &&
        !container.is(e.target) &&
        container.has(e.target).length === 0 &&
        !testMailPanel.is(e.target) &&
        !svgMail.is(e.target) &&
        !iconMailText.is(e.target)
      ) {
        $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap")
          .find(".yay-smtp-test-mail-drawer")
          .css("width", "0");
        $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap")
          .find(".yay-smtp-test-mail-drawer")
          .removeClass("is-open");
        $(yay_smtp_amazonses_class_obj + " .send-test-mail-panel").removeClass(
          "is-active"
        );
      }
      // Close Test email drawer - end

      // Close Email Logs Settings Popup - start
      let containerEmailSettingPopup = $(
        yay_smtp_amazonses_class_obj +
          ".yay-smtp-wrap.mail-logs .components-popover.components-dropdown__content"
      );
      let mailLogSettingsPopupButton = $(
        yay_smtp_amazonses_class_obj +
          ".yay-smtp-wrap.mail-logs .components-dropdown-button"
      );
      let iconMailLogSettings = $(
        yay_smtp_amazonses_class_obj +
          ".yay-smtp-wrap.mail-logs span.dashicons-ellipsis"
      );
      // if the target of the click isn't the container nor a descendant of the container
      if (
        containerEmailSettingPopup.hasClass("is-opened") &&
        mailLogSettingsPopupButton.hasClass("is-opened") &&
        containerEmailSettingPopup.has(e.target).length === 0 &&
        !containerEmailSettingPopup.is(e.target) &&
        !mailLogSettingsPopupButton.is(e.target) &&
        !iconMailLogSettings.is(e.target)
      ) {
        mailLogSettingsPopupButton.removeClass("is-opened");
        containerEmailSettingPopup.removeClass("is-opened");
      }
      // Close Email Logs Settings Popup - end

      // Close email log detail drawer - start
      let containerEmailLogDetail = $(
        yay_smtp_amazonses_class_obj + " .yay-smtp-mail-detail-drawer"
      );
      if (
        containerEmailLogDetail.hasClass("is-open") &&
        !containerEmailLogDetail.is(e.target) &&
        containerEmailLogDetail.has(e.target).length === 0
      ) {
        $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
          .find(".yay-smtp-mail-detail-drawer")
          .css("width", "0");
        $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
          .find(".yay-smtp-mail-detail-drawer")
          .removeClass("is-open");
        $(
          yay_smtp_amazonses_class_obj +
            ".yay-smtp-wrap.mail-logs .yaysmtp-view-btn"
        ).removeClass("is-active");
      }
      // Close email log detail drawer - end

      // Close email log settings drawer - start
      let mailLogSettingsDrawerButton = $(
        yay_smtp_amazonses_class_obj +
          ".yay-smtp-wrap.mail-logs .yaysmtp-email-log-settings"
      );
      let containerEmailLogSettings = $(
        yay_smtp_amazonses_class_obj + " .yay-smtp-mail-log-settings-drawer"
      );

      if (
        containerEmailLogSettings.hasClass("is-open") &&
        mailLogSettingsDrawerButton.hasClass("is-active") &&
        !containerEmailLogSettings.is(e.target) &&
        containerEmailLogSettings.has(e.target).length === 0 &&
        !mailLogSettingsDrawerButton.is(e.target)
      ) {
        $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
          .find(".yay-smtp-mail-log-settings-drawer")
          .css("width", "0");
        $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs")
          .find(".yay-smtp-mail-log-settings-drawer")
          .removeClass("is-open");
        $(
          yay_smtp_amazonses_class_obj +
            " .yay-smtp-button.yaysmtp-email-log-settings"
        ).removeClass("is-active");
      }
      // Close email log settings drawer - end
    });

    //Validate email input when click event
    $(yay_smtp_amazonses_class_obj)
      .find(".yay-smtp-test-mail-address,#yay_smtp_setting_mail_from_email")
      .on("input", function() {
        let elMessage = $(this).siblings(".error-message-email");
        if ($(this).val().length > 0) {
          elMessage.html("").hide();
          YaySmtpAmazonSESValidateEmail($(this).val(), elMessage);
        } else {
          elMessage.html("Email Address is not empty!").show();
        }
      });

    //Init append Icon Mail error on Menu WP
    if (
      $("a.toplevel_page_yaysmtp-amazonses").length > 0 &&
      yay_smtp_amazonses_wp_data.succ_sent_mail_last == "no"
    ) {
      $("a.toplevel_page_yaysmtp-amazonses .wp-menu-name").append(
        '<span class="icon-yaysmtp-sent-mail-error">!</span>'
      );
    }

    // Switch ON/OFF
    if (
      $(
        yay_smtp_amazonses_class_obj +
          " .yay-smtp-card .setting-field .switch input"
      ).is(":checked")
    ) {
      $(
        yay_smtp_amazonses_class_obj +
          " .yay-smtp-card .setting-field .setting-toggle-checked"
      ).show();
    } else {
      $(
        yay_smtp_amazonses_class_obj +
          " .yay-smtp-card .setting-field .setting-toggle-unchecked"
      ).show();
    }

    $(
      yay_smtp_amazonses_class_obj +
        " .yay-smtp-card .setting-field .switch input"
    ).click(function() {
      if ($(this).is(":checked")) {
        $(
          yay_smtp_amazonses_class_obj +
            " .yay-smtp-card .setting-field .setting-toggle-checked"
        ).show();
        $(
          yay_smtp_amazonses_class_obj +
            " .yay-smtp-card .setting-field .setting-toggle-unchecked"
        ).hide();
        $(
          yay_smtp_amazonses_class_obj +
            " .yay-smtp-card .yay_smtp_setting_auth_det"
        ).show();
      } else {
        $(
          yay_smtp_amazonses_class_obj +
            " .yay-smtp-card .setting-field .setting-toggle-checked"
        ).hide();
        $(
          yay_smtp_amazonses_class_obj +
            " .yay-smtp-card .setting-field .setting-toggle-unchecked"
        ).show();
        $(
          yay_smtp_amazonses_class_obj +
            " .yay-smtp-card .yay_smtp_setting_auth_det"
        ).hide();
      }
    });

    $(
      yay_smtp_amazonses_class_obj +
        ".yay-smtp-wrap.mail-logs .setting-field .switch input"
    ).click(function() {
      if ($(this).is(":checked")) {
        $(
          yay_smtp_amazonses_class_obj +
            ".yay-smtp-wrap.mail-logs .setting-el-other-wrap"
        ).show();
        // $(".yay-smtp-wrap.mail-logs .yay-smtp-mail-logs-wrap").show();
      } else {
        $(
          yay_smtp_amazonses_class_obj +
            ".yay-smtp-wrap.mail-logs .setting-el-other-wrap"
        ).hide();
        // $(".yay-smtp-wrap.mail-logs .yay-smtp-mail-logs-wrap").hide();
      }
    });

    //Send Test email
    $(yay_smtp_amazonses_class_obj + " .yay-smtp-send-mail-action").click(
      function() {
        let elMessage = $(
          yay_smtp_amazonses_class_obj +
            " .yay-smtp-test-mail-drawer .error-message-email"
        );
        let emailAddress = $(
          yay_smtp_amazonses_class_obj + " #yay_smtp_test_mail_address"
        ).val();
        if (!emailAddress) {
          elMessage.html("Email Address is not empty!").show();
        } else {
          elMessage.html("").hide();
          if (YaySmtpAmazonSESValidateEmail(emailAddress, elMessage)) {
            $.ajax({
              url: yay_smtp_amazonses_wp_data.YAY_ADMIN_AJAX,
              type: "POST",
              data: {
                action: yay_smtp_amazonses_prefix + "_send_mail",
                nonce: yay_smtp_amazonses_wp_data.ajaxNonce,
                emailAddress: emailAddress
              },
              beforeSend: function() {
                YaySmtpAmazonSESSpinner("yay-smtp-test-mail-drawer", true);
              },
              success: function(result) {
                if (!result.success) {
                  let errorMes = result.data.mess;
                  elMessage.html(errorMes).show();

                  let debugText = result.data.debugText;
                  let html = "<strong>Debug:</strong><br>";
                  html += debugText;

                  // Show error in Send test mail drawer.
                  $(
                    yay_smtp_amazonses_class_obj + " .yay-smtp-debug-text"
                  ).html(html);
                  $(yay_smtp_amazonses_class_obj + " .yay-smtp-debug").show();

                  // Show error in main page.
                  $(
                    yay_smtp_amazonses_class_obj + " .yay-smtp-card-debug-text"
                  ).html(debugText);
                  $(
                    yay_smtp_amazonses_class_obj +
                      " .yay-smtp-card.yay-smtp-card-debug"
                  ).show();

                  if (
                    $("a.toplevel_page_yaysmtp-amazonses").length > 0 &&
                    $(
                      ".toplevel_page_yaysmtp-amazonses .icon-yaysmtp-sent-mail-error"
                    ).length == 0
                  ) {
                    $("a.toplevel_page_yaysmtp-amazonses .wp-menu-name").append(
                      '<span class="icon-yaysmtp-sent-mail-error">!</span>'
                    );
                  }

                  YaySmtpAmazonSESNotification(
                    "Can not send test email",
                    "yay-smtp-wrap"
                  );
                } else {
                  elMessage.html("").hide();
                  $(yay_smtp_amazonses_class_obj + " .yay-smtp-debug").hide();
                  $(
                    yay_smtp_amazonses_class_obj +
                      " .yay-smtp-card.yay-smtp-card-debug"
                  ).hide();
                  $(
                    ".toplevel_page_yaysmtp-amazonses .icon-yaysmtp-sent-mail-error"
                  ).remove();

                  YaySmtpAmazonSESNotification(
                    result.data.mess,
                    "yay-smtp-wrap"
                  );
                }

                YaySmtpAmazonSESSpinner("yay-smtp-test-mail-drawer", false);
              },
              error: function(xhr, status, error) {
                console.log(xhr, status, error);
                if (yay_smtp_amazonses_wp_data.succ_sent_mail_last == "yes") {
                  $(
                    ".toplevel_page_yaysmtp-amazonses .icon-yaysmtp-sent-mail-error"
                  ).remove();
                }
              }
            });
          }
        }
      }
    );

    //Save YaySMTP settings
    $(yay_smtp_amazonses_class_obj + " .yay-smtp-save-settings-action").click(
      function() {
        let fromEmail = $(
          yay_smtp_amazonses_class_obj + " #yay_smtp_setting_mail_from_email"
        ).val();
        let fromName = $(
          yay_smtp_amazonses_class_obj + " #yay_smtp_setting_mail_from_name"
        ).val();
        let mailerProvider = "amazonses"; //$("select.smtper-choose option:checked").val();
        let mailerSettings = {};
        let mailerSettingsEls = $(
          yay_smtp_amazonses_class_obj +
            " .yay-smtp-mailer-settings .yay-settings:visible"
        );

        let forceFromEmail =
          $(
            yay_smtp_amazonses_class_obj +
              " #yay_smtp_setting_mail_force_from_email"
          ).prop("checked") == true
            ? 1
            : 0;
        let forceFromName =
          $(
            yay_smtp_amazonses_class_obj +
              " #yay_smtp_setting_mail_force_from_name"
          ).prop("checked") == true
            ? 1
            : 0;

        if (mailerSettingsEls.length > 0) {
          $.each(mailerSettingsEls, function() {
            let setting = $(this).attr("data-setting");
            let elType = $(this).attr("type");

            if (typeof setting !== typeof undefined && setting !== false) {
              let settingVal = $(this).val();
              if (typeof elType !== typeof undefined && elType === "radio") {
                if ($(this).is(":checked")) {
                  mailerSettings[setting] = settingVal;
                }
              } else if (
                typeof elType !== typeof undefined &&
                elType === "checkbox"
              ) {
                if ($(this).is(":checked")) {
                  mailerSettings[setting] = "yes";
                } else {
                  mailerSettings[setting] = "no";
                }
              } else {
                mailerSettings[setting] = settingVal;
              }
            }
          });
        }

        if (!mailerProvider) {
          alert("Mailer Provider is not empty!");
        } else {
          $.ajax({
            url: yay_smtp_amazonses_wp_data.YAY_ADMIN_AJAX,
            type: "POST",
            data: {
              action: yay_smtp_amazonses_prefix + "_save_settings",
              nonce: yay_smtp_amazonses_wp_data.ajaxNonce,
              settings: {
                fromEmail: fromEmail,
                fromName: fromName,
                forceFromEmail,
                forceFromName,
                mailerProvider: mailerProvider,
                mailerSettings: mailerSettings
              }
            },
            beforeSend: function() {
              YaySmtpAmazonSESSpinner("yay-smtp-wrap", true);
            },
            success: function(result) {
              YaySmtpAmazonSESNotification(result.data.mess, "yay-smtp-wrap");
              YaySmtpAmazonSESSpinner("yay-smtp-wrap", false);
              setTimeout(function() {
                location.reload();
              }, 1500);
            }
          });
        }
      }
    );

    // Show/hide Mail settings page or Mail logs page
    var yaySmtpAmazonSESCurrentPage = getYaySmtpAmazonSESCookie(
      "yay_smtp_amazonses_current_page"
    );
    if (yaySmtpAmazonSESCurrentPage == 1 || yaySmtpAmazonSESCurrentPage == "") {
      $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs").hide();
      $(
        yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.send-mail-settings-wrap"
      ).show();
    } else {
      $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs").show();
      $(
        yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.send-mail-settings-wrap"
      ).hide();
      loadFirstYaySMTPAmazonSESLogsList();
    }
    $(
      yay_smtp_amazonses_class_obj +
        " .yay-smtp-button.panel-tab-btn.mail-logs-button"
    ).click(function(e) {
      e.preventDefault();
      $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs").show();
      $(
        yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.send-mail-settings-wrap"
      ).hide();
      setYaySmtpAmazonSESCookie("yay_smtp_amazonses_current_page", 2, 1); // Mail logs page
      loadFirstYaySMTPAmazonSESLogsList();
    });
    $(yay_smtp_amazonses_class_obj + " .mail-setting-redirect, " + yay_smtp_amazonses_class_obj + " .dashicons-arrow-left-alt").click(function(
      e
    ) {
      e.preventDefault();
      $(yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs").hide();
      $(
        yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.send-mail-settings-wrap"
      ).show();
      setYaySmtpAmazonSESCookie("yay_smtp_amazonses_current_page", 1, 1); // Mail settings page
    });
  });
})(window.jQuery);

function YaySmtpAmazonSESValidateEmail(mail, elMessage) {
  if (
    /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(
      mail
    )
  ) {
    elMessage.html("").hide();
    return true;
  }
  elMessage.html("You have entered an invalid email address!").show();
  return false;
}

function YaySmtpAmazonSESSpinner(containerClass, isShow) {
  let spinnerHtml = '<div class="yay-smtp-spinner">';
  spinnerHtml +=
    '<svg class="woocommerce-spinner" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">';
  spinnerHtml +=
    '<circle class="woocommerce-spinner__circle" fill="none" stroke-width="5" stroke-linecap="round" cx="50" cy="50" r="30"></circle>';
  spinnerHtml += "/<svg>";
  spinnerHtml += "</div>";
  if (isShow) {
    jQuery("." + containerClass).append(spinnerHtml);
  } else {
    jQuery(".yay-smtp-spinner").remove();
  }
}

function YaySmtpAmazonSESNotification(messages, containerClass) {
  let notifyHtml =
    '<div class="yay-smtp-notification"><div class="yay-smtp-notification-content">' +
    messages +
    "</div></div>";

  jQuery("." + containerClass).after(notifyHtml);
  setTimeout(function() {
    jQuery(".yay-smtp-notification").addClass("NslideDown");
    jQuery(".yay-smtp-notification").remove();
  }, 1500);
}

function YaySmtpAmazonSESEmailLogsList(param) {
  jQuery.ajax({
    url: yay_smtp_amazonses_wp_data.YAY_ADMIN_AJAX,
    type: "POST",
    data: {
      action: yay_smtp_amazonses_prefix + "_email_logs",
      nonce: yay_smtp_amazonses_wp_data.ajaxNonce,
      params: param
    },
    beforeSend: function() {
      YaySmtpAmazonSESSpinner("yaysmtp-body", true);
    },
    success: function(results) {
      let data = results.data.data;
      let showColSettings = results.data.showColSettings;
      let showSubjectColClass = showColSettings.showSubjectCol ? "" : "hiden";
      let showToColClass = showColSettings.showToCol ? "" : "hiden";
      let showStatusColClass = showColSettings.showStatusCol ? "" : "hiden";
      let showDatetimeColClass = showColSettings.showDatetimeCol ? "" : "hiden";
      let showActionColClass = showColSettings.showActionCol ? "" : "hiden";

      let html = "";
      data.forEach(function(item) {
        let emailTo = item.email_to;
        let status = "Success";
        let status_cl = "email-success";
        if (parseInt(item.status) == 0) {
          status = "Fail";
          status_cl = "email-fail";
        } else if (parseInt(item.status) == 2) {
          status = "Waiting";
          status_cl = "email-waiting";
        }

        html += "<tr>";
        html += '<th scope="row" class="table-item is-checkbox-column">';
        html += "<div>";
        html += '<span class="checkbox-control-input-container">';
        html +=
          '<input class="checkbox-control-input checkbox-control-input-el" type="checkbox" value="' +
          item.id +
          '">';
        html += "</span>";
        html += "</div>";
        html += "</th>";
        html +=
          '<td class="table-item is-left-aligned subject-col ' +
          showSubjectColClass +
          '">';
        html +=
          '<a class="" data-id="' + item.id + '">' + item.subject + "</a>";
        html += "</td>";

        html +=
          '<td class="table-item is-left-aligned is-sorted to-col ' +
          showToColClass +
          '">';
        emailTo.forEach(function(email_val) {
          html += "<span>" + email_val + "</span><br>";
        });
        html += "</td>";

        html +=
          '<td class="table-item is-left-aligned status-col ' +
          showStatusColClass +
          '">';
        html +=
          '<mark class="email-status ' +
          status_cl +
          '"><span>' +
          status +
          "</span></mark>";
        html += "</td>";

        html +=
          '<td class="table-item datetime-col ' + showDatetimeColClass + '">';
        html += "<span>" + item.date_time + "</span>";
        html += "</td>";

        html += '<td class="table-item action-col ' + showActionColClass + '">';
        html +=
          '<div class="yay-tooltip view-action"><button type="button" class="yaysmtp-btn yaysmtp-view-btn" data-id="' +
          item.id +
          '"><span class="dashicons dashicons-visibility icon-action"></span><span class="yay-tooltiptext yay-tooltip-top">View this email</span></button></div>';
        html +=
          '<div class="yay-tooltip delete-action"><button type="button" class="yaysmtp-btn yaysmtp-delete-btn" data-id="' +
          item.id +
          '"><span class="dashicons dashicons-trash icon-action"></span><span class="yay-tooltiptext yay-tooltip-top">Delete this email</span></button></div>';
        html += "</td>";

        html += "</tr>";
      });

      if (html == "") {
        html +=
          '<tr><td class="table-empty-item" colspan="6">No data to display</td></tr>';
      }

      jQuery(yay_smtp_amazonses_class_obj + " .yaysmtp-body").html(html);

      /** current page - start */
      jQuery(
        yay_smtp_amazonses_class_obj + " .yay-smtp-content .pag-page-current"
      ).val(results.data.currentPage);
      jQuery(
        yay_smtp_amazonses_class_obj + " .yay-smtp-content .pag-page-current"
      ).attr("max", results.data.totalPage);
      /** previous, next button - end */

      /** previous, next button - start */
      let htmlPageRowLabel =
        "Page " + results.data.currentPage + " of " + results.data.totalPage;
      jQuery(
        yay_smtp_amazonses_class_obj +
          " .yay-smtp-content .pagination-page-arrows-label"
      ).html(htmlPageRowLabel);

      if (parseInt(results.data.currentPage) == 1) {
        jQuery(
          yay_smtp_amazonses_class_obj +
            " .yay-smtp-content .pagination-page-arrows-buttons .previous-btn"
        ).prop("disabled", true);
      } else {
        jQuery(
          yay_smtp_amazonses_class_obj +
            " .yay-smtp-content .pagination-page-arrows-buttons .previous-btn"
        ).prop("disabled", false);
      }

      if (
        parseInt(results.data.currentPage) ==
          parseInt(results.data.totalPage) ||
        parseInt(results.data.currentPage) > parseInt(results.data.totalPage)
      ) {
        jQuery(
          yay_smtp_amazonses_class_obj +
            " .yay-smtp-content .pagination-page-arrows-buttons .next-btn"
        ).prop("disabled", true);
      } else {
        jQuery(
          yay_smtp_amazonses_class_obj +
            " .yay-smtp-content .pagination-page-arrows-buttons .next-btn"
        ).prop("disabled", false);
      }
      /** previous, next button - end */
      YaySmtpAmazonSESSpinner("yaysmtp-body", false);
    }
  });
}

function YaySmtpAmazonSESGetParam(param) {
  var query = window.location.search.substring(1);
  var vars = query.split("&");
  for (var i = 0; i < vars.length; i++) {
    var pair = vars[i].split("=");
    if (pair[0] == param) {
      return pair[1];
    }
  }
}

function searchYaySmtpAmazonSESConditionBasicCurrent() {
  let limit = jQuery(
    yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs .pag-per-page-sel"
  ).val();
  let page = jQuery(
    yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs .pag-page-current"
  ).val();
  let valSearch = jQuery(
    yay_smtp_amazonses_class_obj +
      ".yay-smtp-wrap.mail-logs .yay-button-wrap .search .search-imput"
  ).val();
  let status;
  if (
    jQuery(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_status_not_send").is(
      ":checked"
    )
  ) {
    if (
      jQuery(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_status_sent").is(
        ":checked"
      )
    ) {
      status = "all";
    } else {
      status = "not_send";
    }
  } else {
    if (
      jQuery(yay_smtp_amazonses_class_obj + " #yaysmtp_logs_status_sent").is(
        ":checked"
      )
    ) {
      status = "sent";
    } else {
      status = "empty";
    }
  }
  let param = {
    page: parseInt(page),
    limit: parseInt(limit),
    valSearch: valSearch,
    status: status
  };
  return param;
}

function setYaySmtpAmazonSESCookie(cname, cvalue, exdays) {
  var d = new Date();
  d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
  var expires = "expires=" + d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getYaySmtpAmazonSESCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(";");
  for (var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == " ") {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}
function loadFirstYaySMTPAmazonSESLogsList() {
  let page = jQuery(
    yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs .pag-page-current"
  ).val();
  let limit = jQuery(
    yay_smtp_amazonses_class_obj + ".yay-smtp-wrap.mail-logs .pag-per-page-sel"
  ).val();
  let param = {
    page: parseInt(page),
    limit: parseInt(limit)
  };
  YaySmtpAmazonSESEmailLogsList(param);
}
