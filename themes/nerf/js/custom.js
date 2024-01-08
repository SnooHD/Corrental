(function ($) {
  document.addEventListener("DOMContentLoaded", () => {
    /**
     * Booking
     **/

    if (!document.querySelector(".booking_form_div")) return;

    // Select the node that will be observed for mutations
    const targetNodes = document.querySelectorAll(
      ".wpdev-form-control-wrap, .wpbc_change_over_triangle"
    );

    // Options for the observer (which mutations to observe)
    const config = { attributes: false, childList: true, subtree: false };

    // Callback function to execute when mutations are observed
    const callback = (mutationList, observer) => {
      for (const mutation of mutationList) {
        if (mutation.type === "childList") {
          const errorElement =
            mutation.target.querySelector(".wpbc_fe_message_warning") ||
            mutation.target.querySelector(".wpbc_fe_message_error");

          if (!errorElement) continue;

          const isVisible = getComputedStyle(errorElement).display !== "none";
          console.log(isVisible);
          if (isVisible) {
            setTimeout(() => {
              document
                .querySelector(".booking_form_div .loader")
                .classList.remove("active");
            }, 0);
          }
        }
      }
    };

    // Create an observer instance linked to the callback function
    const observer = new MutationObserver(callback);

    // Start observing the target node for configured mutations
    targetNodes.forEach((targetNode) => {
      observer.observe(targetNode, config);
    });

    document
      .querySelector('.booking_form_div input[type="button"]')
      .addEventListener("click", () => {
        document
          .querySelector(".booking_form_div .loader")
          .classList.add("active");
      });
  });
})(jQuery);
