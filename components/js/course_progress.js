jQuery(document).ready(function ($) {
  $("#nav-primary-menu li:eq(1)").after(
    `<li><a href='${phpVars.progressURL}'>${phpVars.progressLinkText}</a></li>`
  );

  $(
    "#main #content .toc>li, #main #content .toc__chapters>li, #page nav.reading-header__inside .toc li"
  ).each(function () {
    const id = $(this).attr("id").split("-").pop();
    const $icon = $(getProgressIcon(isCompleted(phpVars.progress, id)));
    $icon.attr("id", getIconElementId(id));
    $(this).find(">.toc__title__container").prepend($icon);
  });

  $("#hfh-course-progress-chapter-complete").submit(function (e) {
    e.preventDefault(e);

    const formData = new FormData(this);
    formData.append("_ajax_nonce", phpVars.ajaxNonce);
    formData.append("action", "hfh_chapter_complete");

    $.ajax({
      async: true,
      type: "POST",
      url: phpVars.ajaxURL,
      data: formData,
      cache: false,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          $("#hfh-course-progress-chapter-complete input[name=value]").val(
            !response.data.completed
          );
          $("#hfh-course-progress-chapter-complete button[type=submit]")
            .text(
              response.data.completed
                ? phpVars.setIncompleteText
                : phpVars.setCompleteText
            )
            .toggleClass("hfh-button--primary hfh-button--secondary");
          const progress = response.data.progress;
          progress.parts.forEach((part) => {
            if (part.ID) {
              updateProgressIcon(part.ID, isCompleted(progress, part.ID));
            }
            part.completion.chapters.forEach((chapter) => {
              updateProgressIcon(chapter.ID, isCompleted(progress, chapter.ID));
            });
          });
        } else {
          console.log("error");
        }
      },
      error: function (request, status, error) {
        console.log("error");
      },
    });
  });

  function updateProgressIcon(id, complete) {
    const elementId = getIconElementId(id);
    $indicator = $(`#${elementId}`);
    $newIndicator = $(getProgressIcon(complete));
    $newIndicator.attr("id", elementId);
    $indicator.replaceWith($newIndicator);
  }

  function getIconElementId(id) {
    return `hfh-progress-${id}`;
  }

  function getProgressIcon(isCompleted) {
    const completed =
      '<svg class="hfh-chapter__progress-indicator hfh-chapter__progress-indicator--complete" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>';
    const incompleted =
      '<svg class="hfh-chapter__progress-indicator" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" stroke-width="1"><circle cx="10" cy="10" r="7.5" /></svg>';

    return isCompleted ? completed : incompleted;
  }

  function isCompleted(progress, id) {
    const part = progress.parts.find((part) => part.ID == id);
    if (part) {
      return part.completion.total == part.completion.complete;
    }
    const chapter = progress.parts
      .reduce((acc, cur) => {
        acc.push(...cur.completion.chapters);
        return acc;
      }, [])
      .find((chapter) => chapter.ID == id);
    return chapter.complete;
  }
});
