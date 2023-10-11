/**
 * Whenever a H5P module emits an "answered" event, check if the user has gained access to the current post. If yes, reload the page.
 */
if (!PRESSBOOKS_HFH_GAMIPRESS_RESTRICT_CONTENT_H5P_RELOAD.accessGranted) {
  window.addEventListener("DOMContentLoaded", () => {
    H5P.externalDispatcher.on("xAPI", function (event) {
      if (
        event.data.statement.verb.id ===
        "http://adlnet.gov/expapi/verbs/answered"
      ) {
        setTimeout(async () => {
          const postResult = await jQuery.post(
            PRESSBOOKS_HFH_GAMIPRESS_RESTRICT_CONTENT_H5P_RELOAD.ajaxUrl,
            {
              action: "pressbooks_hfh_gamipress_restrict_content_check_access",
              postId:
                PRESSBOOKS_HFH_GAMIPRESS_RESTRICT_CONTENT_H5P_RELOAD.postId,
            }
          );

          if (
            postResult.data.access &&
            !PRESSBOOKS_HFH_GAMIPRESS_RESTRICT_CONTENT_H5P_RELOAD.accessGranted
          ) {
            window.location.reload();
          }
        }, 500);
      }
    });
  });
}
