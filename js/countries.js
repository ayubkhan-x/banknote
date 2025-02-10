document.addEventListener("DOMContentLoaded", function () {
    fetch("get_countries.php")
        .then(response => response.json())
        .then(data => {
            if (!Array.isArray(data)) {
                throw new Error("Ожидался массив, но пришло: " + JSON.stringify(data));
            }

            const container = document.querySelector(".countries-list");
            let groupedCountries = {};

            // Группируем страны по первой букве
            data.forEach(country => {
                const firstLetter = country.name.charAt(0).toUpperCase();
                if (!groupedCountries[firstLetter]) {
                    groupedCountries[firstLetter] = [];
                }
                groupedCountries[firstLetter].push(country);
            });

            let listHTML = "";

            // Генерируем HTML для каждой буквы
            Object.keys(groupedCountries).sort().forEach(letter => {
                let countries = groupedCountries[letter];
                let columns = [[], [], []];

                // Распределяем страны по трем колонкам
                countries.forEach((country, index) => {
                    let colIndex = index % 3;

                    // Формируем HTML для связанных стран
                    let linkedHTML = "";
                    if (country.linked_countries && country.linked_countries.length > 0) {
                        linkedHTML = `<span>(see also `;
                        linkedHTML += country.linked_countries.map(linked =>
                            `<img src="/images/country-flag/${linked.flag_icon || 'images/flag.png'}" alt="${linked.name}"> 
                             <a href="${linked.slug}">${linked.name}</a>`
                        ).join(", ");
                        linkedHTML += `)</span>`;
                    }

                    columns[colIndex].push(`
                        <div class="list-item">
                            <img src="/images/country-flag/${country.flag_icon || 'images/flag.png'}" alt="${country.name}">
                            <div class="list-item-in">
                                <a href="${country.slug}">${country.name}</a>
                                ${linkedHTML}
                            </div>
                        </div>
                    `);
                });

                listHTML += `
                    <div class="countries-list">
                        <div class="list-word word-${letter.toLowerCase()}">${letter}</div>
                        <div class="row">
                            <div class="col-lg-4">${columns[0].join('')}</div>
                            <div class="col-lg-4">${columns[1].join('')}</div>
                            <div class="col-lg-4">${columns[2].join('')}</div>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = listHTML;
        })
        .catch(error => console.error("Ошибка загрузки стран:", error));
});
