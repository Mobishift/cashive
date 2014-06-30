(function() {
  window.Crowdhoster = {
    init: function() {
      $('.show_loader').on("click", function() {
        var $this, target;
        $this = $(this);
        target = $this.attr('data-loader');
        return $('.loader').filter('[data-loader="' + target + '"]').show();
      });
      $('.show_tooltip').tooltip();
      return $.fn.serializeObject = function() {
        var arrayData, objectData;
        arrayData = this.serializeArray();
        objectData = {};
        $.each(arrayData, function() {
          var value;
          if (this.value != null) {
            value = this.value;
          } else {
            value = '';
          }
          if (objectData[this.name] != null) {
            if (!objectData[this.name].push) {
              objectData[this.name] = [objectData[this.name]];
            }
            return objectData[this.name].push(value);
          } else {
            return objectData[this.name] = value;
          }
        });
        return objectData;
      };
    }
  };

  $(function() {
    Crowdhoster.init();
    Crowdhoster.admin.init();
    return Crowdhoster.campaigns.init();
  });

}).call(this);
