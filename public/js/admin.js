(function() {
  Crowdhoster.admin = {
    init: function() {
      var d, h, isSecurityCheckWarningDisplayed, t, _base, _this;
      isSecurityCheckWarningDisplayed = false;
      _this = this;
      $('#settings_custom_css').on("change", function(e) {
        var occ_msg;
        occ_msg = Crowdhoster.admin.checkSafety('settings_custom_css');
        return Crowdhoster.admin.checkSafetyAlert(occ_msg, 'settings_custom_css', 'settings_custom_css_alert');
      });
      $('#settings_custom_js').on("change", function(e) {
        var occ_msg;
        occ_msg = Crowdhoster.admin.checkSafety('settings_custom_js');
        return Crowdhoster.admin.checkSafetyAlert(occ_msg, 'settings_custom_js', 'settings_custom_js_alert');
      });
      $('legend.foldable').on('click', function(e) {
        return $(this).parent().find('div.foldable').slideToggle();
      });
      $('div.foldable').not('.default_expanded').hide();
      if (typeof (_base = $('#campaign_expiration_date')).datetimepicker === "function") {
        _base.datetimepicker({
          timeFormat: "h:mm tt",
          minDate: new Date()
        });
      }
      d = $('#campaign_expiration_date').val();
      if (d && d.length > 0) {
        d = new Date(d);
        h = d.getHours();
        if (h > 12) {
          t = (h - 12) + ':' + ("0" + d.getMinutes()).slice(-2) + ' pm';
        } else {
          if (h === 0) {
            h = 12;
          }
          t = h + ':' + ("0" + d.getMinutes()).slice(-2) + ' am';
        }
        $('#campaign_expiration_date').val($.datepicker.formatDate('mm/dd/yy', d) + ' ' + t);
      }
      if ($('#campaign_expiration_date').length) {
        $('#campaign_expiration_date')[0].defaultValue = $('#campaign_expiration_date').val();
      }
      $('input#campaign_collect_additional_info').on("change", function() {
        return $('.additional_info_input').slideToggle();
      });
      $('input#campaign_include_comments').on("change", function() {
        return $('.include_comments_input').slideToggle();
      });
      $('input[name="campaign[media_type]"]').on("change", function() {
        $('#video-options').slideToggle();
        return $('#image-options').slideToggle();
      });
      $('input#campaign_payment_type_any').on("change", function() {
        $('#preset-amount').slideUp();
        $('#min-amount').slideUp();
        $('#no-rewards').slideUp();
        $('#rewards').slideDown();
        $('#campaign_collect_shipping_message').hide();
        $('#campaign_collect_shipping_warning').show();
        return $('#global-shipping-check').hide();
      });
      $('input#campaign_payment_type_fixed').on("change", function() {
        $('#min-amount').slideUp();
        $('#preset-amount').slideDown();
        $('#rewards').slideUp();
        $('#no-rewards').slideDown();
        $('#global-shipping').slideDown();
        $('#global-shipping-check').show();
        $('#campaign_collect_shipping_message').show();
        return $('#campaign_collect_shipping_warning').hide();
      });
      $('input#campaign_payment_type_min').on("change", function() {
        $('#preset-amount').slideUp();
        $('#min-amount').slideDown();
        $('#no-rewards').slideUp();
        $('#rewards').slideDown();
        $('#campaign_collect_shipping_message').hide();
        $('#campaign_collect_shipping_warning').show();
        return $('#global-shipping-check').hide();
      });
      $('input#goal_type_dollars').on("change", function() {
        $('input#campaign_payment_type_min').attr('disabled', false);
        $('input#campaign_payment_type_any').attr('disabled', false);
        $('#flexible_payment_options').show();
        $('.amount_input').slideDown();
        return $('.orders_input').slideUp();
      });
      $('input#goal_type_orders').on("change", function() {
        $('input#campaign_payment_type_fixed').trigger('click');
        $('input#campaign_payment_type_min').attr('disabled', true);
        $('input#campaign_payment_type_any').attr('disabled', true);
        $('#flexible_payment_options').hide();
        $('.amount_input').slideUp();
        return $('.orders_input').slideDown();
      });
      $('#reward-add').on('click', function(e) {
        var $element, position;
        e.preventDefault();
        $element = $('#rewards li:last-child').clone();
        position = parseInt($('#rewards li').length, 10);
        $element.find('input[name*="price"]').val('0').attr('name', "reward[" + position + "][price]");
        $element.find('input[name*="title"]').val('').attr('name', "reward[" + position + "][title]");
        $element.find('input[name*="image_url"]').val('').attr('name', "reward[" + position + "][image_url]");
        $element.find('textarea[name*="description"]').val('').attr('name', "reward[" + position + "][description]");
        $element.find('input[name*="delivery_date"]').val('').attr('name', "reward[" + position + "][delivery_date]");
        $element.find('input[name*="number"]').val('').attr('name', "reward[" + position + "][number]");
        $element.find('input[name*="collect_shipping_flag"]').attr('name', "reward[" + position + "][collect_shipping_flag]");
        $element.find('input[name*="include_claimed"]').attr('name', "reward[" + position + "][include_claimed]");
        $element.find('input[name*="delete"]').attr('name', "reward[" + position + "][delete]");
        return $element.appendTo('#rewards ul');
      });
      $('.faq.sortable').sortable({
        stop: function(e, ui) {
          var iterator;
          iterator = 1;
          return $.each($('.faq.sortable li'), function() {
            var $this;
            $this = $(this);
            $this.find('input[name*="sort_order"]').val(iterator).attr('name', 'faq[' + iterator + '][sort_order]');
            $this.find('textarea[name*="question"]').attr('name', 'faq[' + iterator + '][question]');
            $this.find('textarea[name*="answer"]').attr('name', 'faq[' + iterator + '][answer]');
            $this.find('.faq_index').html(iterator);
            return iterator++;
          });
        }
      });
      $('#faq-add').on('click', function(e) {
        var $element, position;
        e.preventDefault();
        $element = $('.faq.sortable li:last-child').clone();
        position = parseInt($element.find('.faq_index').html(), 10) + 1;
        $element.find('.faq_index').html(position);
        $element.find('input[name*="sort_order"]').val(position).attr('name', 'faq[' + position + '][sort_order]');
        $element.find('textarea[name*="question"]').html('').attr('name', 'faq[' + position + '][question]');
        $element.find('textarea[name*="answer"]').html('').attr('name', 'faq[' + position + '][answer]');
        return $element.appendTo('.faq.sortable');
      });
      $('.faq.sortable').on('click', function(e) {
        var $this, iterator;
        $this = $(e.target);
        if ($this.is('.faq-delete')) {
          e.preventDefault();
          $this.parent().remove();
          iterator = 1;
          return $.each($('.faq.sortable li'), function() {
            $this = $(this);
            $this.find('.faq_index').html(iterator);
            $this.find('input[name="faq[][sort_order]"]').val(iterator);
            return iterator++;
          });
        }
      });
      return $('.refund-payment').on('click', function(e) {
        var amount, cell, confirm, email, loader, origColor, paymentId, row, status, total, user_fee_amount;
        row = $(this).parent().parent();
        cell = $(this).parent();
        paymentId = row.find('td.ct_payment_id').text();
        email = row.find('td.email').text();
        amount = parseFloat(row.find('td.amount').text().split('$')[1]);
        user_fee_amount = parseFloat(row.find('td.user_fee_amount').text().split('$')[1]);
        total = amount + user_fee_amount;
        confirm = window.confirm("Are you sure you want to refund " + email + " for $" + total.toFixed(2) + "?");
        if (confirm) {
          loader = row.find('td > .loader');
          loader.show();
          status = row.find('td.status');
          origColor = row.find('td.ct_payment_id').css("background-color");
          return $.post("/admin/payments/" + paymentId + "/refund").done(function() {
            status.animate({
              backgroundColor: '#5cb85c'
            }, 400, 'swing', function() {
              return $(this).animate({
                backgroundColor: origColor
              });
            }).text('refunded');
            return cell.html('');
          }).fail(function() {
            status.animate({
              backgroundColor: '#d9534f'
            }, 300, 'swing');
            return setTimeout(function() {
              return alert('Sorry, this payment could not be refunded. These funds may have already been released to you. If you have any questions, please contact open@crowdtilt.com with the payment ID.');
            }, 300);
          }).always(function() {
            return loader.hide();
          });
        }
      });
    },
    checkSafety: function(editor) {
      var occ_href, occ_msg, occ_orig, reg, regDisp, result, str;
      reg = new RegExp(/(\s*[:]*?[=]?\s*["]?\s*\b(http)\s*:\s*\/\/[a-zA-Z0-9+&@#\/%?=~_-|!,;:.~-]*)/g);
      regDisp = new RegExp(/(\b(http)\s*:\s*\/\/[a-zA-Z0-9+&@#\/%?=~_-|!,;:.~-]*)/g);
      str = $("#" + editor).val();
      occ_msg = "";
      while ((result = reg.exec(str)) !== null) {
        occ_orig = str.split(result[1]).length - 1;
        occ_href = str.split("href" + result[1]).length - 1;
        if (occ_orig !== occ_href) {
          occ_msg = occ_msg + "<strong>" + result[1].match(regDisp) + "</strong><br />";
        }
      }
      return occ_msg;
    },
    checkSafetyAlert: function(occ_msg, element, alertElement) {
      if (occ_msg !== '') {
        $('#' + alertElement).html('It looks you are trying to load external content using insecure (non-HTTPS) links. Unless you change the following links to be served over HTTPS, your contributors will see a security warning in their browser.<br />' + occ_msg + '<br />If you need any help with this, please contact open@crowdtilt.com');
        $('#' + alertElement).slideDown();
        $('#' + element).addClass('text-area-border');
        return Crowdhoster.admin.isSecurityCheckWarningDisplayed = false;
      } else {
        $('#' + alertElement).slideUp();
        $('#' + element).removeClass('text-area-border');
        if (Crowdhoster.admin.checkSafety('settings_custom_css') === '') {
          return $('#settings_custom_alert').hide();
        }
      }
    },
    submitWebsiteForm: function(form) {
      return form.submit();
    },
    submitCampaignForm: function(form) {
      var $date;
      $date = $('#campaign_expiration_date');
      $date.val(new Date($date.val()).toUTCString());
      return form.submit();
    },
    submitBankForm: function(form) {
      var $form, userData;
      $('#errors').html('');
      $('#bank_routing_number').removeAttr('name');
      $('#account_number').removeAttr('name');
      $form = $(form);
      userData = {
        name: $form.find('#full_legal_name').val(),
        phone: $form.find('#phone').val(),
        street_address: $form.find('#street_address').val(),
        postal_code: $form.find('#zip').val(),
        dob: $form.find('#birth_year').val() + '-' + $form.find('#birth_month').val()
      };
      return $.ajax('/ajax/verify', {
        type: 'POST',
        data: userData,
        beforeSend: function(jqXHR, settings) {
          return jqXHR.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
        },
        success: function(data) {
          if (data === "success") {
            return Crowdhoster.admin.createBankAccount($form);
          } else {
            $('#errors').append('<p>An error occurred, please re-enter your account information</p>');
            $('.loader').hide();
            $('#bank_routing_number').attr('name', 'bank_routing_number');
            return $('#account_number').attr('name', 'account_number');
          }
        }
      });
    },
    createBankAccount: function($form) {
      var bankData, errors, user_id;
      bankData = {
        account_number: $form.find('#account_number').val(),
        name: $form.find('#full_legal_name').val(),
        bank_code: $form.find('#bank_routing_number').val()
      };
      errors = {};
      if (!crowdtilt.bank.validateUSARoutingNumber(bankData.bank_code)) {
        errors["bank_routing_number"] = "Invalid routing number";
      }
      if (bankData.account_number === '') {
        errors["bank_account_number"] = "Invalid account number";
      }
      if (!$.isEmptyObject(errors)) {
        $.each(errors, function(index, value) {
          return $('#errors').append('<p>' + value + '</p>');
        });
        $('.loader').hide();
        $('#bank_routing_number').attr('name', 'bank_routing_number');
        return $('#account_number').attr('name', 'account_number');
      } else {
        user_id = $form.find('#ct_user_id').val();
        return crowdtilt.bank.create(user_id, bankData, Crowdhoster.admin.bankResponseHandler);
      }
    },
    bankResponseHandler: function(response) {
      var form, input, token;
      switch (response.status) {
        case 201:
          token = response.bank.id;
          input = $('<input name="ct_bank_id" value="' + token + '" type="hidden" />');
          form = document.getElementById('admin_bank_form');
          form.appendChild(input[0]);
          return form.submit();
        default:
          $('#errors').append('<p>An error occurred. Please try again.</p>');
          $('.loader').hide();
          $('#bank_routing_number').attr('name', 'bank_routing_number');
          return $('#account_number').attr('name', 'account_number');
      }
    }
  };

}).call(this);
