<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

$host = 'mysql8';
$user = '39330634_banknotewiki';
$pass = 'eWgl1bn8';
$db_name = '39330634_banknotewiki';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_errno) {
	die("Ошибка подключения: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$slug = $conn->real_escape_string($_GET['slug'] ?? '');
$country_result = $conn->query("SELECT id FROM countries WHERE slug = '$slug' LIMIT 1");
if (!$country_result || $country_result->num_rows === 0)
	die("Страна не найдена.");
$country_id = $country_result->fetch_assoc()['id'];

// Получение списка эр
$eras = $conn->query("SELECT * FROM c_era WHERE country_id = $country_id ORDER BY order_id ASC")->fetch_all(MYSQLI_ASSOC);

// Получение списка валют
$all_currencies = $conn->query("SELECT * FROM c_currency WHERE country_id = $country_id ORDER BY order_id ASC")->fetch_all(MYSQLI_ASSOC);

// Обработка фильтра
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['apply_filter'])) {
		$selected_eras = array_map('intval', $_POST['era'] ?? []);
		$posted_currencies = array_map('intval', $_POST['currency'] ?? []);

		if (!empty($selected_eras)) {
			$valid_currencies = $conn->query("SELECT id FROM c_currency WHERE era_id IN (" . implode(',', $selected_eras) . ")")
				->fetch_all(MYSQLI_ASSOC);
			$valid_currency_ids = array_column($valid_currencies, 'id');
			$selected_currencies = array_intersect($posted_currencies, $valid_currency_ids);
		} else {
			$selected_currencies = $posted_currencies;
		}

		$_SESSION['filter_params'] = [
			'selected_eras' => $selected_eras,
			'selected_currencies' => $selected_currencies,
			'demonetized' => $_POST['demonetized'] ?? 'all',
			'commemorative' => $_POST['commemorative'] ?? 'all'
		];
	} elseif (isset($_POST['clear_filter'])) {
		// Очищаем фильтры и обновляем страницу
		unset($_SESSION['filter_params']);
		header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
		exit;
	}
}


$filter_params = $_SESSION['filter_params'] ?? [];
$selected_eras = array_map('intval', $filter_params['selected_eras'] ?? []);
$selected_currencies = array_map('intval', $filter_params['selected_currencies'] ?? []);
$demonetized = $filter_params['demonetized'] ?? 'all';
$commemorative = $filter_params['commemorative'] ?? 'all';

// Формируем условия запроса
$where = ["c.country_id = $country_id"];

if (!empty($selected_eras)) {
	$where[] = "c.era_id IN (" . implode(',', $selected_eras) . ")";
}

if (!empty($selected_currencies)) {
	$where[] = "c.id IN (" . implode(',', $selected_currencies) . ")";
}

$sql_currencies = "SELECT c.* FROM c_currency c WHERE " . implode(' AND ', $where) . " ORDER BY c.order_id ASC";
$currencies = $conn->query($sql_currencies)->fetch_all(MYSQLI_ASSOC);

$currency_ids = array_column($currencies, 'id');
$issues = [];
if (!empty($currency_ids)) {
	$issues = $conn->query("SELECT * FROM c_issue WHERE currency_id IN (" . implode(',', $currency_ids) . ") ORDER BY order_id ASC")
		->fetch_all(MYSQLI_ASSOC);
}

$banknotes = [];
$issue_ids = array_column($issues, 'id');
if (!empty($issue_ids)) {
	$banknote_where = ["issue_id IN (" . implode(',', $issue_ids) . ")"];

	if ($demonetized !== 'all') {
		$banknote_where[] = "is_demonetized = " . ($demonetized === 'yes' ? 1 : 0);
	}

	if ($commemorative !== 'all') {
		$banknote_where[] = "is_commemorative = " . ($commemorative === 'yes' ? 1 : 0);
	}

	$sql_banknotes = "SELECT * FROM banknotes WHERE " . implode(' AND ', $banknote_where) . " ORDER BY order_id ASC";
	$banknotes = $conn->query($sql_banknotes)->fetch_all(MYSQLI_ASSOC);
}

$varieties_grouped = [];
$banknote_ids = array_column($banknotes, 'id');
if (!empty($banknote_ids)) {
	$varieties = $conn->query("SELECT * FROM b_varieties WHERE banknote_id IN (" . implode(',', $banknote_ids) . ") ORDER BY order_id ASC")
		->fetch_all(MYSQLI_ASSOC);
	foreach ($varieties as $variety) {
		$varieties_grouped[$variety['banknote_id']][] = $variety;
	}
}

$conn->close();
?>



<div class="col-xl-3 col-lg-5 sidebar">
	<form method="GET" class="aside-filter" id="filter-form">
		<input type="hidden" name="slug" value="<?= htmlspecialchars($slug) ?>">

		<!-- Фильтр эр -->
		<div class="filter-block">
			<div class="side-title spy" id="era">
				<p>Era</p>
			</div>
			<div class="side-checks">
				<?php foreach ($eras as $era): ?>
					<label class="control control--checkbox">
						<input type="checkbox" name="era[]" value="<?= $era['id'] ?>" <?= in_array($era['id'], $_GET['era'] ?? []) ? 'checked' : '' ?>>
						<div class="control__indicator"></div>
						<span><?= htmlspecialchars($era['name']) ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Фильтр валют -->
		<div class="filter-block">
			<div class="side-title spy" id="currency">
				<p>Currency</p>
			</div>
			<div class="side-checks">
				<?php foreach ($all_currencies as $currency): ?>
					<?php
					$disabled = !empty($_GET['era']) && !in_array($currency['era_id'], $_GET['era']);
					?>
					<label class="control control--checkbox <?= $disabled ? 'disabled' : '' ?>">
						<input type="checkbox" name="currency[]" value="<?= $currency['id'] ?>" <?= in_array($currency['id'], $_GET['currency'] ?? []) ? 'checked' : '' ?> 	<?= $disabled ? 'disabled' : '' ?>>
						<div class="control__indicator"></div>
						<span><?= htmlspecialchars($currency['name']) ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Фильтр Demonetized -->
		<div class="filter-block">
			<div class="side-title spy" id="demonetized">
				<p>Demonetized</p>
			</div>
			<div class="side-checks">
				<?php foreach (["all" => "All", "yes" => "Yes", "no" => "No"] as $value => $label): ?>
					<label class="control control--checkbox">
						<input type="radio" name="demonetized" value="<?= $value ?>" <?= ($_GET['demonetized'] ?? 'all') === $value ? 'checked' : '' ?>>
						<div class="control__indicator"></div>
						<span><?= $label ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Фильтр Commemorative -->
		<div class="filter-block">
			<div class="side-title spy" id="commemorative">
				<p>Commemorative</p>
			</div>
			<div class="side-checks">
				<?php foreach (["all" => "All", "yes" => "Yes", "no" => "No"] as $value => $label): ?>
					<label class="control control--checkbox">
						<input type="radio" name="commemorative" value="<?= $value ?>" <?= ($_GET['commemorative'] ?? 'all') === $value ? 'checked' : '' ?>>
						<div class="control__indicator"></div>
						<span><?= $label ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="filter-btn">
			<button type="submit" name="apply_filter" class="orange-btn">Apply</button>
			<a href="?slug=<?= htmlspecialchars($slug) ?>" class="clear-btn">Clear</a>
		</div>
	</form>

</div>

<script>
	document.addEventListener('DOMContentLoaded', function () {
		let urlParams = new URLSearchParams(window.location.search);

		// Функция для установки состояния чекбоксов
		function restoreCheckboxState(name) {
			let values = urlParams.getAll(name + '[]'); // Получаем все значения массивов (era[], currency[])
			document.querySelectorAll(`input[name="${name}[]"]`).forEach(cb => {
				cb.checked = values.includes(cb.value);
			});
		}

		// Восстанавливаем чекбоксы "Era" и "Currency"
		restoreCheckboxState('era');
		restoreCheckboxState('currency');

		// Восстанавливаем состояние радиокнопок (demonetized, commemorative)
		['demonetized', 'commemorative'].forEach(name => {
			let value = urlParams.get(name);
			if (value) {
				let radio = document.querySelector(`input[name="${name}"][value="${value}"]`);
				if (radio) radio.checked = true;
			}
		});

		// Деактивация валют, если выбрана эра
		let selectedEras = urlParams.getAll('era[]');
		if (selectedEras.length > 0) {
			document.querySelectorAll('input[name="currency[]"]').forEach(cb => {
				if (!selectedEras.includes(cb.dataset.era)) {
					cb.disabled = true;
				}
			});
		}

		// Кнопка "Clear" теперь полностью очищает фильтр
		document.querySelector('.clear-btn').addEventListener('click', function (e) {
			e.preventDefault();
			window.location.href = window.location.pathname + '?slug=' + urlParams.get('slug');
		});
	});

</script>