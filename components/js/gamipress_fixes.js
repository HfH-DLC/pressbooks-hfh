/**
 * Whenever a H5P module emits an "answered" event, check if the user has gained access to the current post. If yes, reload the page.
 * This ajax request also happens to trigger the notifications check we want.
 */

window.addEventListener("DOMContentLoaded", () => {
  H5P.externalDispatcher.on("xAPI", function (event) {
    if (
      event.data.statement.verb.id === "http://adlnet.gov/expapi/verbs/answered"
    ) {
      jQuery.ajax("/");
    }
  });
});
