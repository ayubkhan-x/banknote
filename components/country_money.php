<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = '';
$user = '';
$pass = '';
$db_name = '';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_errno) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Получаем slug страны из URL
$slug = isset($_GET['slug']) ? $conn->real_escape_string($_GET['slug']) : '';

if (!$slug) {
    die("Страна не найдена.");
}

// Получаем данные о валюте выбранной страны
$sql = "SELECT * FROM c_currency WHERE country_id = (SELECT id FROM countries WHERE slug = '$slug') ORDER BY order_id ASC";
$currency_result = $conn->query($sql);
$currencies = $currency_result->fetch_all(MYSQLI_ASSOC);

// Получаем все выпуски валюты
$sql = "SELECT * FROM c_issue ORDER BY order_id ASC";
$issue_result = $conn->query($sql);
$issues = $issue_result->fetch_all(MYSQLI_ASSOC);

// Получаем все банкноты
$sql = "SELECT * FROM banknotes ORDER BY order_id ASC";
$banknote_result = $conn->query($sql);
$banknotes = $banknote_result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<div class="row main-content">
    <div class="col-xl-9 col-lg-12 content">
        <div class="money-tab">
            <div class="tab-top">
                <div class="tab-inner">
                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="pills-full-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-full" type="button" role="tab" aria-controls="pills-full"
                                aria-selected="true">
                                <svg width="17" height="14" viewBox="0 0 17 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M0.955964 2.00195C1.48393 2.00195 1.91193 1.55424 1.91193 1.00195C1.91193 0.449668 1.48393 0.00195312 0.955964 0.00195312C0.428 0.00195312 0 0.449668 0 1.00195C0 1.55424 0.428 2.00195 0.955964 2.00195ZM3.82393 0.201953C3.3821 0.201953 3.02393 0.560125 3.02393 1.00195C3.02393 1.44378 3.3821 1.80195 3.82393 1.80195H16.2515C16.6933 1.80195 17.0515 1.44378 17.0515 1.00195C17.0515 0.560125 16.6933 0.201953 16.2515 0.201953H3.82393ZM1.91193 7.00195C1.91193 7.55424 1.48393 8.00195 0.955964 8.00195C0.428 8.00195 0 7.55424 0 7.00195C0 6.44967 0.428 6.00195 0.955964 6.00195C1.48393 6.00195 1.91193 6.44967 1.91193 7.00195ZM3.82393 6.20195C3.3821 6.20195 3.02393 6.56013 3.02393 7.00195C3.02393 7.44378 3.3821 7.80195 3.82393 7.80195H16.2515C16.6933 7.80195 17.0515 7.44378 17.0515 7.00195C17.0515 6.56013 16.6933 6.20195 16.2515 6.20195H3.82393ZM1.91193 13.002C1.91193 13.5542 1.48393 14.002 0.955964 14.002C0.428 14.002 0 13.5542 0 13.002C0 12.4497 0.428 12.002 0.955964 12.002C1.48393 12.002 1.91193 12.4497 1.91193 13.002ZM3.82393 12.202C3.3821 12.202 3.02393 12.5601 3.02393 13.002C3.02393 13.4438 3.3821 13.802 3.82393 13.802H16.2515C16.6933 13.802 17.0515 13.4438 17.0515 13.002C17.0515 12.5601 16.6933 12.202 16.2515 12.202H3.82393Z"
                                        fill="#181818" />
                                </svg>
                                <span>Full</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pills-grid-tab" data-bs-toggle="pill"
                                data-bs-target="#pills-grid" type="button" role="tab" aria-controls="pills-grid"
                                aria-selected="false">
                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M1.57305 2C1.57305 1.79372 1.7204 1.7 1.81421 1.7H7.26514C7.35896 1.7 7.50631 1.79372 7.50631 2V6.78999C7.50631 6.99627 7.35896 7.08999 7.26514 7.08999H1.81421C1.7204 7.08999 1.57305 6.99627 1.57305 6.78999V2ZM1.57305 11.2083C1.57305 11.0021 1.7204 10.9083 1.81421 10.9083H7.26514C7.35896 10.9083 7.50631 11.0021 7.50631 11.2083V16C7.50631 16.2063 7.35896 16.3 7.26514 16.3H1.81421C1.7204 16.3 1.57305 16.2063 1.57305 16V11.2083ZM10.2397 2C10.2397 1.79372 10.387 1.7 10.4808 1.7H15.9318C16.0256 1.7 16.1729 1.79372 16.1729 2V6.79166C16.1729 6.99793 16.0256 7.09166 15.9317 7.09166H10.4808C10.387 7.09166 10.2397 6.99793 10.2397 6.79166V2ZM10.2398 11.2083C10.2398 11.0021 10.3871 10.9083 10.481 10.9083H15.9319C16.0257 10.9083 16.173 11.0021 16.173 11.2083V16C16.173 16.2063 16.0257 16.3 15.9319 16.3H10.481C10.3871 16.3 10.2398 16.2063 10.2398 16V11.2083Z"
                                        stroke="#787878" stroke-width="1.4" stroke-linejoin="round" />
                                </svg>
                                <span>Grid</span>
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-full" role="tabpanel">
                            <div class="tab-block">

                                <?php foreach ($currencies as $currency): ?>
                                    <div class="tab-title spy" id="currency-<?= $currency['id']; ?>">
                                        <h3 class="title-tab">
                                            <?php if (!empty($currency['ext_link'])): ?>
                                                <a href="<?= htmlspecialchars($currency['ext_link']); ?>">
                                                    <?= htmlspecialchars($currency['ext_link_text']); ?>
                                                </a>
                                            <?php else: ?>
                                                <?= htmlspecialchars($currency['name']); ?>
                                            <?php endif; ?>
                                        </h3>
                                    </div>
                                    <div class="note">
                                        <a href="#!"><img src="/images/clip-icon.svg" alt=""> Please follow the link to find
                                            Soviet banknotes</a>
                                    </div>
                                    <?php foreach ($issues as $issue): ?>
                                        <?php if ($issue['currency_id'] == $currency['id']): ?>
                                            <div class="full-box">
                                                <span class="full-title spy" id="issue-<?= $issue['id']; ?>">
                                                    <?= htmlspecialchars($issue['name']); ?>
                                                </span>

                                                <?php foreach ($banknotes as $banknote): ?>
                                                    <?php if ($banknote['issue_id'] == $issue['id']): ?>
                                                        <div class="full-item">
                                                            <div class="full-img">
                                                                <span><img src="/images/<?= htmlspecialchars($banknote['img_back']); ?>"
                                                                        alt=""></span>
                                                                <span><img src="/images/<?= htmlspecialchars($banknote['img_front']); ?>"
                                                                        alt=""></span>
                                                            </div>
                                                            <div class="full-block">
                                                                <div class="money-name">
                                                                    <span><?= htmlspecialchars($banknote['ref1'] ?? ''); ?></span>
                                                                    <span><?= htmlspecialchars($banknote['ref2'] ?? ''); ?></span>
                                                                    <span><?= htmlspecialchars($banknote['ref3'] ?? ''); ?></span>
                                                                    <a href="#"> <?= htmlspecialchars($banknote['name']); ?> </a>
                                                                </div>
                                                                <div class="money-type">
                                                                    <span>
                                                                        <?= $banknote['is_commemorative'] ? 'Commemorative banknote' : 'Standart banknote'; ?>
                                                                    </span>
                                                                </div>
                                                                <div class="money-issued">
                                                                    <span>Issued by: </span>
                                                                    <p>Riga City Council</p>
                                                                </div>
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Image</th>
                                                                            <th>Variation</th>
                                                                            <th>Date</th>
                                                                            <th>Description</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                <div class="table-img">
                                                                                    <img src="/images/money-back.png" alt="">
                                                                                    <img src="/images/money-front.png" alt="">

                                                                                    <div class="modal-body">
                                                                                        <div class="modal-top row">
                                                                                            <div class="col-lg-2">
                                                                                                <div id="thumbnail-container"
                                                                                                    class="mod-img">
                                                                                                    <img src="//images/modal-money-back.png"
                                                                                                        alt="" class="thumbnail"
                                                                                                        data-large="/images/modal-money-back.png">
                                                                                                    <img src="/images/modal-money-front.png"
                                                                                                        alt="" class="thumbnail"
                                                                                                        data-large="/images/modal-money-front.png">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-2">
                                                                                                <span class="mod-type">P#1(1)</span>
                                                                                            </div>
                                                                                            <div class="col-lg-2">
                                                                                                <span class="mod-date">1992-May-25</span>
                                                                                            </div>
                                                                                            <div class="col-lg-6">
                                                                                                <span class="mod-desc">Old spelling of
                                                                                                    50,000 with 3E. Solid security thread
                                                                                                    with</span>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="image-popup hidden">
                                                                                            <img class="image-viewer"
                                                                                                src="/images/modal-money-back.png" alt="">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>P#1(1)</td>
                                                                            <td>1992-May-25</td>
                                                                            <td>Old spelling of 50,000 with 3E. Solid security thread with
                                                                            </td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>
                                                                                <div class="table-img">
                                                                                    <img src="/images/money-back.png" alt="">
                                                                                    <img src="/images/money-front.png" alt="">
                                                                                    <div class="modal-body">
                                                                                        <div class="modal-top row">
                                                                                            <div class="col-lg-2">
                                                                                                <div id="thumbnail-container"
                                                                                                    class="mod-img">
                                                                                                    <img src="/images/modal-money-back.png"
                                                                                                        alt="" class="thumbnail"
                                                                                                        data-large="/images/modal-money-back.png">
                                                                                                    <img src="/images/modal-money-front.png"
                                                                                                        alt="" class="thumbnail"
                                                                                                        data-large="/images/modal-money-front.png">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-2">
                                                                                                <span class="mod-type">P#1(1)</span>
                                                                                            </div>
                                                                                            <div class="col-lg-2">
                                                                                                <span class="mod-date">1992-May-25</span>
                                                                                            </div>
                                                                                            <div class="col-lg-6">
                                                                                                <span class="mod-desc">Old spelling of
                                                                                                    50,000 with 3E. Solid security thread
                                                                                                    with</span>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="image-popup hidden">
                                                                                            <img class="image-viewer"
                                                                                                src="/images/modal-money-back.png" alt="">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>P#1(1)</td>
                                                                            <td>1992-May-25</td>
                                                                            <td>Watermark: Forward "S" vertical braids pattern</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>
                                                                                <div class="table-img">
                                                                                    <img src="/images/money-back.png" alt="">
                                                                                    <img src="/images/money-front.png" alt="">
                                                                                    <div class="modal-body">
                                                                                        <div class="modal-top row">
                                                                                            <div class="col-lg-2">
                                                                                                <div id="thumbnail-container"
                                                                                                    class="mod-img">
                                                                                                    <img src="/images/modal-money-back.png"
                                                                                                        alt="" class="thumbnail"
                                                                                                        data-large="/images/modal-money-back.png">
                                                                                                    <img src="/images/modal-money-front.png"
                                                                                                        alt="" class="thumbnail"
                                                                                                        data-large="/images/modal-money-front.png">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-2">
                                                                                                <span class="mod-type">P#1(1)</span>
                                                                                            </div>
                                                                                            <div class="col-lg-2">
                                                                                                <span class="mod-date">1992-May-25</span>
                                                                                            </div>
                                                                                            <div class="col-lg-6">
                                                                                                <span class="mod-desc">Old spelling of
                                                                                                    50,000 with 3E. Solid security thread
                                                                                                    with</span>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="image-popup hidden">
                                                                                            <img class="image-viewer"
                                                                                                src="/images/modal-money-back.png" alt="">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>P#1(1)</td>
                                                                            <td>1992-May-25</td>
                                                                            <td>Watermark: Forward "S" vertical braids pattern</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>
                                                                                <div class="table-img">
                                                                                    <img src="/images/money-back.png" alt="">
                                                                                    <img src="/images/money-front.png" alt="">
                                                                                    <div class="modal-body">
                                                                                        <div class="modal-top row">
                                                                                            <div class="col-lg-2">
                                                                                                <div id="thumbnail-container"
                                                                                                    class="mod-img">
                                                                                                    <img src="/images/modal-money-back.png"
                                                                                                        alt="" class="thumbnail"
                                                                                                        data-large="/images/modal-money-back.png">
                                                                                                    <img src="/images/modal-money-front.png"
                                                                                                        alt="" class="thumbnail"
                                                                                                        data-large="/images/modal-money-front.png">
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-lg-2">
                                                                                                <span class="mod-type">P#1(1)</span>
                                                                                            </div>
                                                                                            <div class="col-lg-2">
                                                                                                <span class="mod-date">1992-May-25</span>
                                                                                            </div>
                                                                                            <div class="col-lg-6">
                                                                                                <span class="mod-desc">Old spelling of
                                                                                                    50,000 with 3E. Solid security thread
                                                                                                    with</span>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="image-popup hidden">
                                                                                            <img class="image-viewer"
                                                                                                src="/images/modal-money-back.png" alt="">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>P#1(1)</td>
                                                                            <td>1992-May-25</td>
                                                                            <td>Watermark: Forward "S" vertical braids pattern</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>


                        <div class="tab-pane fade" id="pills-grid" role="tabpanel" aria-labelledby="pills-grid-tab">
                            <div class="tab-block">

                                <?php foreach ($currencies as $currency): ?>
                                    <!-- Header Currency -->
                                    <div class="tab-title">
                                        <h3 class="title-tab spy" id="currency-<?= $currency['id']; ?>">
                                            <?php if (!empty($currency['ext_link'])): ?>
                                                <a href="<?= htmlspecialchars($currency['ext_link']); ?>">
                                                    <?= htmlspecialchars($currency['ext_link_text']); ?>
                                                </a>
                                            <?php else: ?>
                                                <?= htmlspecialchars($currency['name']); ?>
                                            <?php endif; ?>
                                        </h3>
                                    </div>

                                    <?php foreach ($issues as $issue): ?>
                                        <?php if ($issue['currency_id'] == $currency['id']): ?>
                                            <!-- Header Issue -->
                                            <div class="grid-box">
                                                <span class="full-title spy" id="issue-<?= $issue['id']; ?>">
                                                    <?= htmlspecialchars($issue['name']); ?>
                                                </span>
                                                <div class="row grid-rov">

                                                    <?php foreach ($banknotes as $banknote): ?>
                                                        <?php if ($banknote['issue_id'] == $issue['id']): ?>
                                                            <!-- Item -->
                                                            <div class="col-xl-3 col-lg-4 col-md-6">
                                                                <a href="#!" class="grid-item">
                                                                    <span class="grid-img">
                                                                        <img src="/images/<?= htmlspecialchars($banknote['img_back']); ?>"
                                                                            alt="">
                                                                        <img src="/images/<?= htmlspecialchars($banknote['img_front']); ?>"
                                                                            alt="">
                                                                    </span>

                                                                    <span
                                                                        class="grid-tag"><?= htmlspecialchars($banknote['ref1'] ?? ''); ?></span>
                                                                    <span
                                                                        class="grid-tag"><?= htmlspecialchars($banknote['ref2'] ?? ''); ?></span>
                                                                    <span
                                                                        class="grid-tag"><?= htmlspecialchars($banknote['ref3'] ?? ''); ?></span>

                                                                    <span class="grid-text">
                                                                        <span
                                                                            class="grid-name"><?= htmlspecialchars($banknote['name']); ?></span>
                                                                        <span class="grid-types">
                                                                            <span class="grid-type">P#1(1)</span>
                                                                            <span class="grid-type">P#1(1)</span>
                                                                            <span class="grid-type">P#1(1)</span>
                                                                        </span>
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>

                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>

                                <?php endforeach; ?>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="object-count">125 objects</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-5 sidebar">
        <div class="aside-filter">
            <div class="filter-block">
                <div class="side-title spy" id="era">
                    <p>Era</p>
                </div>
                <div class="side-checks">
                    <label class="control control--checkbox">
                        <input type="checkbox">
                        <div class="control__indicator"></div>
                        <span>Republic of Belarus (after 1991)</span>
                    </label>
                </div>
            </div>

            <div class="filter-block">
                <div class="side-title spy" id="currency">
                    <p>Currency</p>
                </div>
                <div class="side-checks">
                    <label class="control control--checkbox">
                        <input type="checkbox">
                        <div class="control__indicator"></div>
                        <span>First Rouble (1992-2000)</span>
                    </label>
                    <label class="control control--checkbox">
                        <input type="checkbox">
                        <div class="control__indicator"></div>
                        <span>Second Rouble (2000-2016)</span>
                    </label>
                    <label class="control control--checkbox">
                        <input type="checkbox">
                        <div class="control__indicator"></div>
                        <span>Third Rouble (2016-now)</span>
                    </label>
                </div>
            </div>

            <div class="filter-block">
                <div class="side-title spy" id="demonetized">
                    <p>Demonetized</p>
                </div>
                <div class="side-checks">
                    <label class="control control--checkbox">
                        <input type="checkbox">
                        <div class="control__indicator"></div>
                        <span>All</span>
                    </label>
                    <label class="control control--checkbox">
                        <input type="checkbox">
                        <div class="control__indicator"></div>
                        <span>Yes</span>
                    </label>
                    <label class="control control--checkbox">
                        <input type="checkbox">
                        <div class="control__indicator"></div>
                        <span>No</span>
                    </label>
                </div>
            </div>

            <div class="filter-block">
                <div class="side-title spy" id="commemorative">
                    <p>Commemorative</p>
                </div>
                <div class="side-checks">
                    <label class="control control--checkbox">
                        <input type="checkbox">
                        <div class="control__indicator"></div>
                        <span>All</span>
                    </label>
                    <label class="control control--checkbox">
                        <input type="checkbox">
                        <div class="control__indicator"></div>
                        <span>Yes</span>
                    </label>
                    <label class="control control--checkbox">
                        <input type="checkbox">
                        <div class="control__indicator"></div>
                        <span>No</span>
                    </label>
                </div>
            </div>

            <div class="filter-btn">
                <button type="button" class="orange-btn">Apply</button>
                <button type="reset" class="clear-btn hidden">Clear Filter</button>
            </div>
        </div>
    </div>
</div>