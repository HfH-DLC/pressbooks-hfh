const languages = [
  { label: "Deutsch", value: "de" },
  { label: "Englisch", value: "en" },
  { label: "Französisch", value: "fr" },
  { label: "Italienisch", value: "it" },
];

(function ($) {
  const languagePrompt = () => {
    const deferred = new jQuery.Deferred();

    dialog = $("<div></div>").dialog({
      autoOpen: false,
      modal: true,
      dialogClass: "hfh-quicktag-dialog",
      buttons: {
        Ok: selectLanguage,
        Abbrechen: function () {
          deferred.reject();
          dialog.dialog("close");
        },
      },
      open: function () {
        $(".ui-widget-overlay").bind("click", function () {
          dialog.dialog("close");
        });

        let optionsMarkup = "";
        languages.forEach((language) => {
          optionsMarkup += `<option value=${language.value}>${language.label}</option>`;
        });

        const markup = `
          <label for="hfh-language" >Bitte wähle eine Sprache aus:</label>
          <select name="hfh-language" id="hfh-language">
            ${optionsMarkup}
          </select>
        `;

        $(this).html(markup);
      },
      close: function () {
        dialog.remove();
      },
    });

    function selectLanguage() {
      const language = dialog.find("select").val();
      deferred.resolve(language);
      dialog.dialog("close");
    }

    dialog.dialog("open");

    return deferred.promise();
  };

  QTags.addButton("language", "Sprache", async (e, c, ed) => {
    try {
      const lang = await languagePrompt();
      if (ed.canvas.selectionStart !== ed.canvas.selectionEnd) {
        this.tagStart = `<span lang="${lang}">`;
        this.tagEnd = "</span>";
      } else {
        this.tagStart = `<span lang="${lang}"></span>`;
      }
    } catch (e) {
      return;
    }

    QTags.TagButton.prototype.callback.call(this, e, c, ed);
  });
})(jQuery);
