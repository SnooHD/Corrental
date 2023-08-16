(function ($) {
  document.addEventListener("DOMContentLoaded", () => {
    /**
     * Booking
     **/

    if (!document.querySelector("#booking_form_div2")) return;

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
          console.log(mutation.target);
          const errorElement = mutation.target.querySelector(".alert");
          console.log(errorElement);
          if (errorElement) {
            setTimeout(() => {
              document
                .querySelector("#booking_form_div2 .loader")
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
      .querySelector('#booking_form_div2 input[type="button"]')
      .addEventListener("click", () => {
        document
          .querySelector("#booking_form_div2 .loader")
          .classList.add("active");
      });
  });
})(jQuery);
