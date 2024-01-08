(function ($) {
  document.addEventListener("DOMContentLoaded", () => {
    const allTranslations = {
      en: {
        "check-in": "Check in",
        "check-out": "Check out",
        cost: "Cost",
        time: "Time",
        date: "Date",
        format: "Time should be 24h",
      },
      nl: {
        "check-in": "Inchecken",
        "check-out": "Uitchecken",
        cost: "Kosten",
        time: "Tijd",
        date: "Datum",
        format: "Tijd moet 24h zijn",
      },
      es: {
        "check-in": "Registrarse",
        "check-out": "Salida",
        cost: "Costo",
        time: "Hora",
        date: "Fecha",
        format: "El tiempo debe ser 24h",
      },
    };

    const lang = document.querySelector("html").getAttribute("lang");
    const language = lang.split("-")[0];
    const translations = allTranslations[language];

    const getDateFromCalendar = (element) => {
      // Date in this calendar is only visible in the html onclick and onmouseover attributes
      const onclickAttr = element.getAttribute("onclick").split(",");
      const dateString = parseInt(
        onclickAttr[onclickAttr.length - 1].replace(")", "")
      );
      return new Date(dateString).toLocaleDateString(lang);
    };

    const getTotalCostFromElements = (elements) => {
      const textFromElements = elements.slice(0, -1).map((elem) => {
        const currency = elem.querySelector(".date-content-bottom").textContent;
        return parseFloat(currency.replace(/[€.\s]+/g, "").replace(",", "."));
      });

      const totalCosts = textFromElements.reduce((prev, cur) => prev + cur, 0);

      const price = new Intl.NumberFormat("nl-NL", {
        style: "currency",
        currency: "EUR",
        useGrouping: false,
      }).format(totalCosts);

      return price.replace(/\s/g, "");
    };

    const showBalloon = (
      target,
      { checkInTime, checkIn, checkOutTime, checkOut, totalCost }
    ) => {
      let content = "";
      if (checkIn) {
        content += `
          <div class="datetime_balloon_title">
            <strong>${translations["check-in"]}</strong>
          </div>
          <table>
            <tbody>
              <tr>
                <td class="datetime_balloon_row">${translations["date"]}:</td>
                <td class="datetime_balloon_row_value">${checkIn}</td>
              </tr>
              <tr>
                <td class="datetime_balloon_row">${translations["time"]}:</td>
                <td class="datetime_balloon_row_value">${checkInTime}</td>
              </tr>
            </tbody>
          </table>
        `;
      }

      if (checkOut) {
        content += `
          <div class="datetime_balloon_title">
            <strong>${translations["check-out"]}</strong>
          </div>
          <table>
            <tbody>
              <tr>
                <td class="datetime_balloon_row_key">${translations["date"]}:</td>
                <td class="datetime_balloon_row_value">${checkOut}</td>
              </tr>
              <tr>
                <td class="datetime_balloon_row_key">${translations["time"]}:</td>
                <td class="datetime_balloon_row_value">${checkOutTime}</td>
              </tr>
            </tbody>
          </table>
        `;
      }

      if (totalCost) {
        content += `
          <table>
            <tbody>
              <tr>
                <td class="datetime_balloon_row_key">${translations["cost"]}:</td>
                <td class="datetime_balloon_row_value">${totalCost}</td>
              </tr>
            </tbody>
          </table>
        `;
      }

      target.insertAdjacentHTML(
        "afterbegin",
        `
          <div class="datetime_balloon">
            <div class="datetime_balloon_wrapper">
              ${content}
            </div>
          </div>
        `
      );
    };

    const dayMouseEnter = (event, inst) => {
      const target = event.currentTarget;

      const resourceId = inst.settings.wpbc_resource_id;
      const checkInTime = document.querySelector(
        `#starttime${resourceId}`
      ).value;
      const checkOutTime = document.querySelector(
        `#endtime${resourceId}`
      ).value;

      const selectedDays = [
        ...document.querySelectorAll(
          ".datepick-one-month table.datepick td.datepick-days-cell.datepick-current-day:not(.datepick-unselectable)"
        ),
      ];

      const hoveredDays = [
        ...document.querySelectorAll(
          ".datepick-one-month table.datepick td.datepick-days-cell.datepick-days-cell-over:not(.datepick-unselectable)"
        ),
      ];

      // keep showing check-in when hovering passed full days
      if (selectedDays.length === 1 && hoveredDays.length === 0) {
        showBalloon(selectedDays[0], {
          checkInTime,
          checkIn: getDateFromCalendar(selectedDays[0]),
          checkOutTime,
          checkOut: null,
          totalCost: 0,
        });

        return;
      }

      // add class for check_in and check_out
      if (hoveredDays.length > 0) {
        hoveredDays[0].classList.add("pre_check_in_time");

        if (hoveredDays.length > 1) {
          hoveredDays.forEach((elem) =>
            elem.classList.remove("pre_check_out_time")
          );
          hoveredDays[hoveredDays.length - 1].classList.add(
            "pre_check_out_time"
          );
        }
      }

      if (
        selectedDays.length === 0 &&
        target.classList.contains("check_in_time")
      ) {
        return;
      }

      if (!hoveredDays.includes(target)) {
        hoveredDays.push(target);
      }

      let checkIn = getDateFromCalendar(hoveredDays[0]);
      let checkOut = null;
      let totalCost = 0;

      if (
        hoveredDays[0].classList.contains("datepick-current-day") &&
        selectedDays.length > 1
      ) {
        totalCost = getTotalCostFromElements(selectedDays);
      }

      if (
        hoveredDays.length === 1 &&
        !hoveredDays[0].classList.contains("pre_check_out_time")
      ) {
        if (hoveredDays[0].classList.contains("datepick-current-day")) {
          if (!hoveredDays[0].classList.contains("pre_check_in_time")) {
            return;
          }
        }

        // checkin hover
        showBalloon(target, {
          checkInTime,
          checkIn,
          checkOutTime,
          checkOut,
          totalCost,
        });
        return;
      }

      checkIn = 0;
      checkOut = getDateFromCalendar(hoveredDays[hoveredDays.length - 1]);
      totalCost = getTotalCostFromElements(hoveredDays);

      if (
        hoveredDays.length === 1 &&
        hoveredDays[0].classList.contains("pre_check_out_time")
      ) {
        totalCost = getTotalCostFromElements(selectedDays);
      }

      showBalloon(target, {
        checkInTime,
        checkIn,
        checkOutTime,
        checkOut,
        totalCost,
      });
    };

    const setMouseEnterEvent = (event, id, inst) => {
      const calendarCells = [
        ...document.querySelectorAll(
          ".datepick-one-month table.datepick td.datepick-days-cell:not(.datepick-unselectable)"
        ),
        ...document.querySelectorAll(
          ".datepick-one-month table.datepick td.datepick-days-cell.full_day_booking"
        ),
      ];

      calendarCells.forEach((elem, _index) => {
        elem.addEventListener("mouseenter", (event) =>
          dayMouseEnter(event, inst)
        );

        elem.addEventListener("mouseleave", (event) => {
          const balloonMessages = [
            ...document.querySelectorAll(
              ".datepick-one-month table.datepick td.datepick-days-cell .datetime_balloon"
            ),
          ];

          balloonMessages.forEach((elem) => elem.remove());
        });

        elem.addEventListener("click", (event) => {
          const selectedDays = [
            ...document.querySelectorAll(
              ".datepick-one-month table.datepick td.datepick-days-cell.datepick-current-day"
            ),
          ];

          if (selectedDays.length === 0) return;

          // add class for check_in and check_out
          selectedDays[0].classList.add("pre_check_in_time");

          if (selectedDays.length > 1) {
            selectedDays.forEach((elem) =>
              elem.classList.remove("pre_check_out_time")
            );
            selectedDays[selectedDays.length - 1].classList.add(
              "pre_check_out_time"
            );

            // update price
            const totalPrice = getTotalCostFromElements(selectedDays);
            document.querySelector(".form_total_booking_cost").innerHTML =
              totalPrice;
          }
        });
      });

      const timeInput = document.querySelector('[name="incheck_time2"]');

      timeInput.addEventListener("input", (event) => {
        const target = event.currentTarget;
        target.setCustomValidity("");
        const val = target.value;

        const regex = /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/.test(val);
        if (regex) {
          document
            .querySelector("#submit_calendar_button")
            .removeAttribute("disabled");
          const resourceId = inst.settings.wpbc_resource_id;
          document.querySelector(`#starttime${resourceId}`).value =
            target.value;
          return;
        }

        document
          .querySelector("#submit_calendar_button")
          .setAttribute("disabled", "true");
        target.setCustomValidity(translations["format"]);
        target.reportValidity();
      });
    };

    jQuery("body").on(
      "wpbc_datepick_inline_calendar_refresh",
      setMouseEnterEvent
    );
  });
})(jQuery);
