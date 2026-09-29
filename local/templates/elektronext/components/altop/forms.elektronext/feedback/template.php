<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();



$this->setFrameMode(true);



use Bitrix\Main\Localization\Loc;



$obName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $this->GetEditAreaId($this->randString()));

$containerName = "feedback-".$obName;?>

<link rel="stylesheet" href="/local/templates/elektronext/js/owlCarousel/owl.carousel.min.css">
<script src="/local/templates/elektronext/js/owlCarousel/owl.carousel.min.js"></script>
<div class="reviews-wrapper">
	<div class="container">
		<div class="reviews reviews--grid">
			<div class="reviews-info">
				<h2 class="reviews-info__title">Нам доверяют</h2>
				<div class="reviews-info__desc">
					<p>Мы стремимся повышать качество услуг, поэтому просим вас оставить свой отзыв в независимом источнике - Яндекс.Картах. Мы обязательно изучим ваше мнение, чтобы сделать сервис еще лучше!</p>
				</div>
				<div class="reviews-info__badge yandex-badge">
					<iframe src="https://yandex.ru/sprav/widget/rating-badge/57814610774" width="150" height="50" style="border: 0px;"></iframe>
					<p><a href="https://yandex.ru/maps/org/santekhpodbor/57814610774/reviews/?from=tabbar&ll=37.486663%2C55.612741&source=serp_navig&z=16.96  " target="_blank" rel="nofollow">Оставьте честный отзыв</a>, и получите скидку 10% </p>
				</div>
			</div>
			<div class="reviews-slider owl-carousel">
				<div class="reviews-slider-slide">
					<span class="reviews-slider-slide__name">Оксана К.</span>
					<div class="reviews-slider-slide__stars">
						<img src="/local/templates/elektronext/images/stars.png" alt="stars" title="stars">
					</div>
					<div class="reviews-slider-slide__desc">
						<p>Хороший магазин с широким ассортиментом! Большое спасибо менеджеру Андрею за грамотную консультацию и помощь в выборе сантехники, а так же за оперативность и профессионализм :) будем обращаться ещё</p>
					</div>
				</div>
				<div class="reviews-slider-slide">
					<span class="reviews-slider-slide__name">Мария Викторовна</span>
					<div class="reviews-slider-slide__stars">
						<img src="/local/templates/elektronext/images/stars.png" alt="stars" title="stars">
					</div>
					<div class="reviews-slider-slide__desc">
						<p>Отличный выбор сантехники,глаза разбегаются от разнообразия. Спасибо персоналу помогли подобрать именно то что нужно,главное без лишнего навязывания.Однозначно рекомендую.</p>
					</div>
				</div>
				<div class="reviews-slider-slide">
					<span class="reviews-slider-slide__name">Ladushka_studio</span>
					<div class="reviews-slider-slide__stars">
						<img src="/local/templates/elektronext/images/stars.png" alt="stars" title="stars">
					</div>
					<div class="reviews-slider-slide__desc">
						<p>Очень рекомендую! Помогли определиться с выбором сантехники, подобрали нужный цвет. Просто чтобы вы понимали, то выбор сантехники и мелочевки к ней, это не картошку выбирать. Часа по 2 точно там каждый раз были. И каждый раз нам терпеливо объясняли и рассказывали. Поэтому подобрали оптимальный вариант. Ребятам огромное спасибо!</p>
					</div>
				</div>
				<div class="reviews-slider-slide">
					<span class="reviews-slider-slide__name">Жанна И.</span>
					<div class="reviews-slider-slide__stars">
						<img src="/local/templates/elektronext/images/stars.png" alt="stars" title="stars">
					</div>
					<div class="reviews-slider-slide__desc">
						<p>Огромная благодарность руководству и менеджеру Андрею за внимательное отношение к своим клиентам. Индивидуальная работа, помощь в выборе, полезные советы, готовность идти навстречу, сопричастность -словом прекрасная компания!</p>
					</div>
				</div>
				<div class="reviews-slider-slide">
					<span class="reviews-slider-slide__name">Андрей Б.</span>
					<div class="reviews-slider-slide__stars">
						<img src="/local/templates/elektronext/images/stars.png" alt="stars" title="stars">
					</div>
					<div class="reviews-slider-slide__desc">
						<p>нающий продавец, который все показал и помог сделать выбор. К сожалению не знаю его имени, но за следующей покупкой обращусь именно к нему. На складе сотрудники проверили качество товара и все погрузили мне в машину. Спасибо!</p>
					</div>
				</div>
				<div class="reviews-slider-slide">
					<span class="reviews-slider-slide__name">Жабил С.</span>
					<div class="reviews-slider-slide__stars">
						<img src="/local/templates/elektronext/images/stars.png" alt="stars" title="stars">
					</div>
					<div class="reviews-slider-slide__desc">
						<p>Идинственный магазин который помог разобраться с мойками для кухни, обьяснили в чем разница и подобрали оптемальную для меня. Однозначно РЕКОМЕНДУЮ!!!! СПАСИБО!!!</p>
					</div>
				</div>
				<div class="reviews-slider-slide">
					<span class="reviews-slider-slide__name">Елена Т.</span>
					<div class="reviews-slider-slide__stars">
						<img src="/local/templates/elektronext/images/stars.png" alt="stars" title="stars">
					</div>
					<div class="reviews-slider-slide__desc">
						<p>Большое спасибо менеджеру Андрею за консультацию и внимательное отношение к клиентам, а так же за оперативность, отзывчивость и готовность идти на встречу !</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="hidden-print feedback-wrapper">

	<div class="container">

		<div class="row feedback" id="<?=$containerName?>">

			<div class="col-xs-12">

				<div class="h1"><?=$arResult["IBLOCK"]["NAME"]?></div>

			</div>

			<form action="javascript:void(0)">

				<input type="hidden" name="IBLOCK_STRING" value="<?=$arResult['IBLOCK']['STRING']?>" />

				<div class="col-xs-12 col-md-12">

					<?foreach($arResult["IBLOCK"]["PROPERTIES"] as $arProp) {

						if($arProp["USER_TYPE"] != "HTML") {

							if($arProp["CODE"] != "SOURCE_URL") {?>

								<div class="form-group<?=(!empty($arProp['HINT']) ? ' has-feedback' : '');?>">

									<input type="text" name="<?=$arProp['CODE']?>" class="form-control" placeholder="<?=$arProp['NAME']?>" inputmode="<?=($arProp['CODE'] == 'PHONE' ? 'numeric' : 'text')?>" />

									<?if(!empty($arProp["HINT"])) {?>

										<i class="form-control-feedback fv-icon-no-has fa <?=$arProp['HINT']?>"></i>

									<?}?>

								</div>

							<?} else {?>

								<input type="hidden" name="<?=$arProp['CODE']?>" value="" />

							<?}

						}

					}

					unset($arProp);?>

					<?if($arParams["USE_CAPTCHA"]) {?>

					<div class="form-group captcha">

						<!-- <div class="pic" style="display:none;">								

							<img src="" width="100" height="36" alt="CAPTCHA" />

						</div>	 -->						

						<input type="text" maxlength="5" name="CAPTCHA_WORD" class="form-control" placeholder="<?=Loc::getMessage('FORMS_FEEDBACK_CAPTCHA_WORD')?>" />

						<input type="hidden" name="CAPTCHA_SID" value="" />

					</div>

					<?}?>

					<div class="form-group<?=(!$arParams['USE_CAPTCHA'] ? ' no-captcha' : '');?>">

						<button onclick="ym(78427446, 'reachGoal', 'otpravka'); return true;" type="submit" class="btn btn-primary"><?=Loc::getMessage("FORMS_FEEDBACK_SUBMIT")?></button>

					</div>

				</div>

				<!--div class="col-xs-12 col-md-6">

					<?foreach($arResult["IBLOCK"]["PROPERTIES"] as $arProp) {

						if($arProp["USER_TYPE"] == "HTML") {?>

							<div class="form-group<?=(!empty($arProp['HINT']) ? ' has-feedback' : '');?>">

								<textarea name="<?=$arProp['CODE']?>" class="form-control" rows="3" placeholder="<?=$arProp['NAME']?>" style="height:<?=$arProp['USER_TYPE_SETTINGS']['height']?>px; min-height:<?=$arProp['USER_TYPE_SETTINGS']['height']?>px; max-height:<?=$arProp['USER_TYPE_SETTINGS']['height']?>px;" inputmode="text"></textarea>

								<?if(!empty($arProp["HINT"])) {?>

									<i class="form-control-feedback fv-icon-no-has fa <?=$arProp['HINT']?>"></i>

								<?}?>

							</div>

						<?}

					}

					unset($arProp);?>

				</div-->

				<?if($arParams["USER_CONSENT"]) {?>

					<input type="hidden" name="USER_CONSENT_ID" value="<?=$arParams['USER_CONSENT_ID']?>" />

					<input type="hidden" name="USER_CONSENT_URL" value="" />

					<div class="col-xs-12">

						<div class="form-group form-group-checkbox">

							<div class="checkbox">

								<?$fields = array();

								foreach($arResult["IBLOCK"]["PROPERTIES"] as $arProp) {

									if($arProp["USER_TYPE"] != "HTML" && $arProp["CODE"] != "SOURCE_URL")

										$fields[] = $arProp["NAME"];

								}

								unset($arProp);?>

								<?$APPLICATION->IncludeComponent("bitrix:main.userconsent.request", "",

									array(

										"ID" => $arParams["USER_CONSENT_ID"],

										"INPUT_NAME" => "USER_CONSENT",

										"IS_CHECKED" => $arParams["USER_CONSENT_IS_CHECKED"],

										"AUTO_SAVE" => "N",

										"IS_LOADED" => $arParams["USER_CONSENT_IS_LOADED"],

										"REPLACE" => array(

											"button_caption" => Loc::getMessage("FORMS_FEEDBACK_SUBMIT"),

											"fields" => $fields

										)

									),

									$component

								);?>

								<?unset($fields);?>

							</div>

						</div>

					</div>

				<?}?>

			</form>

			<div class="col-xs-12">

				<div class="alert" style="display: none;"></div>

			</div>

		</div>

	</div>

</div>



<?$jsProps = array();

foreach($arResult["IBLOCK"]["PROPERTIES"] as $arProp) {

	if($arProp["CODE"] != "OBJECT_ID" && $arProp["CODE"] != "PRODUCT_ID" && $arProp["CODE"] != "OFFER_ID" && $arProp["CODE"] != "SOURCE_URL") {

		$jsProps[$arProp["CODE"]] = array(

			"CODE" => $arProp["CODE"],

			"REQUIRED" => $arProp["IS_REQUIRED"]

		);

	}

}

unset($arProp);?>



<script type="text/javascript">	

	BX.message({

		FORMS_NOT_EMPTY_INVALID: '<?=GetMessageJS("FORMS_FEEDBACK_NOT_EMPTY_INVALID")?>',

		FORMS_PHONE_WRONG: '<?=GetMessageJS("FORMS_FEEDBACK_PHONE_WRONG")?>',

		FORMS_PHONE_INVALID: '<?=GetMessageJS("FORMS_FEEDBACK_PHONE_INVALID")?>',

		FORMS_USER_CONSENT_NOT_EMPTY_INVALID: '<?=GetMessageJS("FORMS_FEEDBACK_USER_CONSENT_NOT_EMPTY_INVALID")?>',

		FORMS_CAPTCHA_WRONG: '<?=GetMessageJS("FORMS_FEEDBACK_CAPTCHA_WRONG")?>',			

		FORMS_ALERT_SUCCESS: '<?=GetMessageJS("FORMS_FEEDBACK_ALERT_SUCCESS")?>',

		FORMS_ALERT_ERROR: '<?=GetMessageJS("FORMS_FEEDBACK_ALERT_ERROR")?>'

	});

	var <?=$obName?> = new JCFormsFeedbackComponent({

		componentPath: '<?=CUtil::JSEscape($componentPath)?>',

		jsProps: <?=CUtil::PhpToJSObject($jsProps)?>,

		defaultCountry: '<?=CUtil::JSEscape($arParams["DEFAULT_COUNTRY"])?>',

		phoneMask: '<?=$arParams["PHONE_MASK"]?>',

		userConsent: '<?=$arParams["USER_CONSENT"]?>',

		useCaptcha: '<?=$arParams["USE_CAPTCHA"]?>',		

		container: '<?=$containerName?>'

	});

</script>



<?unset($jsProps);