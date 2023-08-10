/**
 * Whenever a H5P module emits an "answered" event, make a dummy jQuery ajax request to trigger the Gamipress Notifications listener.
 */
window.addEventListener("DOMContentLoaded", () => {
  H5P.externalDispatcher.on("xAPI", function (event) {
    if (
      event.data.statement.verb.id === "http://adlnet.gov/expapi/verbs/answered"
    ) {
      jQuery.ajax({ url: "/" });
    }
  });
});
