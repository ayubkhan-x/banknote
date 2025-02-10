document.addEventListener("DOMContentLoaded", function () {
  const continentsContainer = document.querySelector(".side-checks.continents");
  const subregionsContainer = document.querySelector(".side-checks.subregions");
  const clearFilterButton = document.querySelector(".clear-btn");

  if (!continentsContainer || !subregionsContainer || !clearFilterButton) {
    console.error(
      "Ошибка: Не найдены контейнеры для континентов, суб-регионов или кнопка очистки."
    );
    return;
  }

  // Загружаем фильтры
  fetch("get_filters.php")
    .then((response) => response.json())
    .then((data) => {
      if (!data.continents || !data.subregions) {
        throw new Error("Некорректный JSON от сервера");
      }

      // Заполняем континенты
      data.continents.forEach((continent) => {
        const label = document.createElement("label");
        label.className = "control control--checkbox";
        label.innerHTML = `
                    <input type="checkbox" class="continent-filter" data-id="${continent.id}">
                    <div class="control__indicator"></div>
                    <span>${continent.name}</span>
                `;
        continentsContainer.appendChild(label);
      });

      // Заполняем суб-регионы
      data.subregions.forEach((subregion) => {
        const label = document.createElement("label");
        label.className = "control control--checkbox";
        label.innerHTML = `
                    <input type="checkbox" class="subregion-filter" data-id="${subregion.id}">
                    <div class="control__indicator"></div>
                    <span>${subregion.name}</span>
                `;
        subregionsContainer.appendChild(label);
      });

      // Добавляем обработчики событий для чекбоксов
      const checkboxes = document.querySelectorAll(
        ".continent-filter, .subregion-filter"
      );
      checkboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", toggleClearButton);
      });
    })
    .catch((error) => console.error("Ошибка загрузки фильтров:", error));

  // Функция для обновления видимости кнопки "Clear Filter"
  function toggleClearButton() {
    const checkedCheckboxes = document.querySelectorAll(
      ".continent-filter:checked, .subregion-filter:checked"
    );
    if (checkedCheckboxes.length > 0) {
      clearFilterButton.classList.remove("hidden");
    } else {
      clearFilterButton.classList.add("hidden");
    }
  }

  // Обработчик для кнопки "Clear Filter"
  clearFilterButton.addEventListener("click", function () {
    // Сбрасываем все чекбоксы
    const checkboxes = document.querySelectorAll(
      ".continent-filter, .subregion-filter"
    );
    checkboxes.forEach((checkbox) => {
      checkbox.checked = false;
    });

    // Скрываем кнопку "Clear Filter"
    clearFilterButton.classList.add("hidden");

    // Обновляем список стран (загружаем все страны)
    fetch("get_countries.php")
      .then((response) => response.json())
      .then((data) => {
        updateCountries(data);
      })
      .catch((error) => console.error("Ошибка загрузки стран:", error));
  });

  // Обработчик для кнопки "Apply Filter"
  document
    .getElementById("apply-filter")
    .addEventListener("click", function () {
      const continentIds = Array.from(
        document.querySelectorAll(".continent-filter:checked")
      ).map((checkbox) => checkbox.dataset.id);
      const subregionIds = Array.from(
        document.querySelectorAll(".subregion-filter:checked")
      ).map((checkbox) => checkbox.dataset.id);

      // Формирование параметров запроса
      const params = new URLSearchParams();
      continentIds.forEach((id) => params.append("continent_id[]", id));
      subregionIds.forEach((id) => params.append("subregion_id[]", id));

      fetch(`get_countries.php?${params}`)
        .then((response) => response.json())
        .then((data) => {
          updateCountries(data);
        })
        .catch((error) => console.error("Ошибка загрузки стран:", error));
    });
});

// Функция для отрисовки стран
function updateCountries(data) {
  const container = document.querySelector(".countries-list");
  if (!container) {
    console.error("Не найден контейнер для стран");
    return;
  }

  // Очищаем предыдущие результаты
  container.innerHTML = "";

  // Группируем страны по первой букве
  const groupedCountries = {};
  data.forEach((country) => {
    const firstLetter = country.name.charAt(0).toUpperCase();
    if (!groupedCountries[firstLetter]) {
      groupedCountries[firstLetter] = [];
    }
    groupedCountries[firstLetter].push(country);
  });

  let listHTML = "";

  // Сортируем буквы и создаем секции
  Object.keys(groupedCountries)
    .sort()
    .forEach((letter) => {
      const countries = groupedCountries[letter];
      const columns = [[], [], []]; // Три колонки

      // Распределяем страны по колонкам
      countries.forEach((country, index) => {
        const colIndex = index % 3;

        // Формируем блок связанных стран
        let linkedHTML = "";
        if (country.linked_countries && country.linked_countries.length > 0) {
          linkedHTML = `<span>(see also `;
          linkedHTML += country.linked_countries
            .map(
              (linked) =>
                `<img src="/images/country-flag/${
                  linked.flag_icon || "images/flag.png"
                }" alt="${linked.name}"> 
                             <a href="${linked.slug}">${linked.name}</a>`
            )
            .join(", ");
          linkedHTML += `)</span>`;
        }

        // Создаем элемент страны
        columns[colIndex].push(`
                    <div class="list-item">
                    <img src="/images/country-flag/${
                      country.flag_icon || "images/flag.png"
                    }" alt="${country.name}">
                        <div class="list-item-in">
                           <a href="${country.slug}">${country.name}</a>
                                ${linkedHTML}
                        </div>
                    </div>
                `);
      });

      // Добавляем секцию с буквой в общий HTML
      listHTML += `
                <div class="country-group">
                    <div class="list-word word-${letter.toLowerCase()}">${letter}</div>
                    <div class="row">
                        ${columns
                          .map(
                            (col) => `
                            <div class="col-lg-4">${col.join("")}</div>
                        `
                          )
                          .join("")}
                    </div>
                </div>
            `;
    });

  container.innerHTML = listHTML;
}
