(function ($) {
  document.addEventListener("DOMContentLoaded", () => {
    $(".ihotspot_hastooltop").each(function () {
      $(this).data("powertip", function () {
        var htmlThis = $(this)
          .parents(".ihotspot_tooltop_html")
          .attr("data-html");
        return htmlThis;
      });
      var thisPlace = $(this)
        .parents(".ihotspot_tooltop_html")
        .data("placement");
      $(this).powerTip({
        placement: thisPlace,
        smartPlacement: true,
        manual: true,
      });
    });

    /**
     * Area interaction
     **/

    let activeItem;
    const items = document.querySelectorAll('[data-id="a66e5ac"] .row .item');
    const pins = document.querySelectorAll(
      '[data-id="d3df568"] .wrap_svl .tips .ihotspot_hastooltop'
    );

    const setActiveItem = (id) => {
      const link = document.querySelector(
        `[data-id="a66e5ac"] .row .item [href="#${id}"]`
      );

      items.forEach((item) => {
        item.classList.remove("active");
      });

      const item = link.parentElement.parentElement.parentElement.parentElement;
      item.classList.add("active");
    };

    const setActivePin = (id) => {
      $.powerTip.hide();
      $.powerTip.show(document.querySelector(`#${id} .ihotspot_hastooltop`));
    };

    const activateItem = (id) => {
      if (activeItem === id) return;

      setActiveItem(id);
      setActivePin(id);

      activeItem = id;
    };

    pins.forEach((pin) => {
      const id = pin.parentElement.parentElement.id;
      pin.addEventListener("mouseover", () => {
        activateItem(id);
      });
    });

    items.forEach((item) => {
      const id = item.querySelector("a")?.getAttribute("href").replace("#", "");

      if (!id) return;

      item.addEventListener("mouseover", () => {
        activateItem(id);
      });
    });

    setTimeout(() => {
      activateItem("bar");
    }, 1000);
  });
})(jQuery);
