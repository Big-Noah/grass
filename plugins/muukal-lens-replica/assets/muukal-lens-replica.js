(function () {
	'use strict';

	var config = window.muukalLensReplicaConfig || {};
	var schema = config.config || {};
	var usageImages = {
		1: 'https://img.muukal.com//img/home/frame/usage_1.jpg',
		2: 'https://img.muukal.com//img/home/frame/usage_2.jpg',
		3: 'https://img.muukal.com//img/home/frame/usage_3.jpg',
		4: 'https://img.muukal.com//img/home/frame/usage_4.jpg',
		5: 'https://img.muukal.com//img/home/frame/usage_5.jpg',
		20: 'https://img.muukal.com//img/home/frame/usage_20.jpg'
	};
	var lensTypeImages = {
		1: 'https://img.muukal.com//img/home/frame/lens_type_1.jpg',
		3: 'https://img.muukal.com//img/home/frame/lens_type_3.jpg',
		4: 'https://img.muukal.com//img/home/frame/lens_type_4.jpg',
		6: 'https://img.muukal.com//img/home/frame/lens_type_6.jpg',
		7: 'https://img.muukal.com//img/home/frame/lens_type_7.jpg',
		8: 'https://img.muukal.com//img/home/frame/lens_type_8.jpg'
	};
	var lensIndexImages = {
		2: 'https://img.muukal.com//img/home/frame/lens_index_2.jpg',
		3: 'https://img.muukal.com//img/home/frame/lens_index_3.jpg',
		4: 'https://img.muukal.com//img/home/frame/lens_index_4.jpg',
		5: 'https://img.muukal.com//img/home/frame/lens_index_5.jpg'
	};
	var coatingImages = {
		1: 'https://img.muukal.com//img/home/frame/coating_1.jpg',
		2: 'https://img.muukal.com//img/home/frame/coating_2.jpg',
		3: 'https://img.muukal.com//img/home/frame/coating_3.jpg'
	};

	function money(value) {
		return '$' + Number(value || 0).toFixed(2);
	}

	function createOption(option, selected) {
		return '<option value="' + option.value + '"' + (String(option.value) === String(selected) ? ' selected' : '') + '>' + option.label + '</option>';
	}

	function absMax(values) {
		return Math.max.apply(null, values.map(function (value) {
			return Math.abs(Number(value || 0));
		}));
	}

	function renderPhoto(url, fallback) {
		if (url) {
			return '<img class="mlr-photo-img" src="' + url + '" alt="">';
		}
		return '<div class="mlr-photo">' + fallback + '</div>';
	}

	function init(root) {
		var openButton = root.querySelector('#hadStock');
		var container = root.querySelector('#lens_container');
		var closeButtons = root.querySelectorAll('[data-close]');
		var payloadPreview = root.querySelector('.mlr-payload-preview');
		var copyButton = root.querySelector('.mlr-copy-payload');
		var submitButton = root.querySelector('#lens-add-cart');
		var editAgain = root.querySelector('#edit-again');
		var status = root.querySelector('.mlr-status');
		var upgradeModal = root.querySelector('.mlr-upgrade-modal');
		var upgradeCopy = root.querySelector('.mlr-upgrade-copy');
		var upgradeAccept = root.querySelector('.mlr-upgrade-accept');
		var upgradeSkip = root.querySelector('.mlr-upgrade-skip');

		var state = {
			openStep: 1,
			usage: 1,
			readers: 0,
			power: '0',
			lenstype: 0,
			lenstype_color: 0,
			lensindex: 0,
			coating: 0,
			pdkey: 1,
			nearpd: 0,
			prism: 0,
			bluelight: false,
			payload: null,
			upgradePrompted: false,
			form: {
				od_sph: '0.00',
				os_sph: '0.00',
				od_cyl: '0.00',
				os_cyl: '0.00',
				od_axis: '',
				os_axis: '',
				od_add: '0',
				os_add: '0',
				pd: '63',
				od_pd: '0',
				os_pd: '0',
				npd: '0',
				birth_year: '0',
				od_prismnum_v: '0',
				os_prismnum_v: '0',
				od_prismdir_v: '0',
				os_prismdir_v: '0',
				od_prismnum_h: '0',
				os_prismnum_h: '0',
				od_prismdir_h: '0',
				os_prismdir_h: '0',
				lens_comment: '',
				rx_name: 'prescription March 2026'
			}
		};

		function setStatus(message) {
			status.textContent = message || '';
		}

		function getUsage() {
			return schema.usage_options.find(function (item) {
				return Number(item.id) === Number(state.usage);
			});
		}

		function getUsagePriceTable() {
			return schema.pricing.lenstype_price[String(state.usage)] || schema.pricing.lenstype_price[state.usage] || {};
		}

		function getIndexPriceTable() {
			return schema.pricing.lensindex_price[String(state.usage)] || schema.pricing.lensindex_price[state.usage] || {};
		}

		function getAllowedLensTypes() {
			var rules = schema.dependency_rules.step3_enabled_by_usage;
			return rules[String(state.usage)] || rules[state.usage] || [];
		}

		function getAllowedLensIndices() {
			var rules = schema.dependency_rules.step4_base_enabled_by_usage;
			var allowed = (rules[String(state.usage)] || rules[state.usage] || []).slice();
			var disabled = [];
			var maxRx = absMax([state.form.od_sph, state.form.os_sph, state.form.od_cyl, state.form.os_cyl]);

			if (state.readers === 1 && state.usage === 2) {
				if (Number(state.power || 0) > 2.75) {
					disabled.push(2);
				}
			} else {
				if (state.usage !== 20 && maxRx > 3.75) {
					disabled.push(2);
				}
				if (state.usage !== 20 && maxRx > 7.75) {
					disabled.push(3);
				}
				if (state.usage !== 20 && maxRx > 10) {
					disabled.push(4);
				}
			}

			if (state.lenstype === 3 && [53, 54, 55].indexOf(Number(state.lenstype_color)) >= 0) {
				disabled.push(5);
			}
			if (state.lenstype === 8) {
				disabled.push(2, 5);
			}
			return { allowed: allowed, disabled: disabled };
		}

		function getLensColorOptions(lenstypeId) {
			return (schema.lens_type_color_map[String(lenstypeId)] || schema.lens_type_color_map[lenstypeId] || []).map(function (colorId) {
				return schema.lens_colors[String(colorId)] || schema.lens_colors[colorId];
			});
		}

		function getLensTypePrice(id) {
			var row = getUsagePriceTable()[String(id)] || getUsagePriceTable()[id];
			return row ? Number(row[4]) : 0;
		}

		function getLensIndexPrice(id) {
			var row = getIndexPriceTable()[String(id)] || getIndexPriceTable()[id];
			return row ? Number(row[1]) : 0;
		}

		function getCoatingPrice(id) {
			var row = schema.pricing.coating_price[String(id)] || schema.pricing.coating_price[id];
			return row ? Number(row[1]) : 0;
		}

		function getPrismPrice() {
			if (!state.prism) {
				return 0;
			}
			var row = schema.pricing.prex_price['1'] || schema.pricing.prex_price[1];
			return row ? Number(row[1]) : 0;
		}

		function getBlueLightPrice() {
			return state.bluelight ? Number(schema.bluelight.price[1]) : 0;
		}

		function getLensTotal() {
			return getLensTypePrice(state.lenstype) + getLensIndexPrice(state.lensindex) + getCoatingPrice(state.coating) + getPrismPrice() + getBlueLightPrice();
		}

		function getGrandTotal() {
			return Number(schema.product.frame_price) + getLensTotal();
		}

		function bluelightLocked() {
			return state.usage === 3 || state.lenstype === 7 || Number(state.lenstype_color) === 52;
		}

		function syncBluelightLock() {
			if (bluelightLocked()) {
				state.bluelight = false;
			}
		}

		function ensureValidSelections() {
			if (getAllowedLensTypes().indexOf(Number(state.lenstype)) === -1) {
				state.lenstype = 0;
				state.lenstype_color = 0;
			}
			var lensIndexRule = getAllowedLensIndices();
			if (lensIndexRule.allowed.indexOf(Number(state.lensindex)) === -1 || lensIndexRule.disabled.indexOf(Number(state.lensindex)) >= 0) {
				state.lensindex = 0;
			}
			syncBluelightLock();
		}

		function getRecommendedIndex() {
			var maxBase = Math.max(Math.abs(Number(state.form.od_sph || 0)), Math.abs(Number(state.form.os_sph || 0)), Math.abs(Number(state.form.od_cyl || 0)), Math.abs(Number(state.form.os_cyl || 0)));
			if (state.readers === 1 && state.usage === 2) {
				var power = Number(state.power || 0);
				if (power > 6.25) {
					return 5;
				}
				if (power > 3.5) {
					return 4;
				}
				if (power > 2) {
					return 3;
				}
				return 2;
			}
			if (maxBase > 8) {
				return 5;
			}
			if (maxBase > 6) {
				return 4;
			}
			if (maxBase > 2.25) {
				return 3;
			}
			return 2;
		}

		function validateStep2() {
			if (state.usage === 20) {
				return true;
			}
			if (state.usage === 2 && state.readers === 1) {
				return Number(state.power || 0) > 0;
			}
			var hasRx = Number(state.form.od_sph || 0) !== 0 || Number(state.form.os_sph || 0) !== 0 || Number(state.form.od_cyl || 0) !== 0 || Number(state.form.os_cyl || 0) !== 0 || Number(state.form.od_add || 0) !== 0 || Number(state.form.os_add || 0) !== 0;
			if (!hasRx) {
				setStatus('Prescription has no value. Use non-prescription if you do not need Rx lenses.');
				return false;
			}
			if ((Number(state.form.od_cyl || 0) !== 0 && !Number(state.form.od_axis || 0)) || (Number(state.form.os_cyl || 0) !== 0 && !Number(state.form.os_axis || 0))) {
				setStatus('CYL requires AXIS for the same eye.');
				return false;
			}
			if (state.pdkey === 2 && (!Number(state.form.od_pd || 0) || !Number(state.form.os_pd || 0))) {
				setStatus('Two-PD mode requires right and left PD.');
				return false;
			}
			if ([3, 4, 5].indexOf(state.usage) >= 0 && (!Number(state.form.od_add || 0) || !Number(state.form.os_add || 0))) {
				setStatus('Bifocal and progressive flows require ADD values.');
				return false;
			}
			return true;
		}

		function validateBeforeSubmit() {
			if (!validateStep2()) {
				return false;
			}
			if (!state.lenstype) {
				setStatus('Choose a lens type.');
				return false;
			}
			if ((schema.lens_types[String(state.lenstype)] || {}).requires_color && !state.lenstype_color) {
				setStatus('This lens type requires a color selection.');
				return false;
			}
			if (!state.lensindex) {
				setStatus('Choose a lens thickness.');
				return false;
			}
			if (!state.coating) {
				setStatus('Choose a coating.');
				return false;
			}
			return true;
		}

		function setOpenStep(step) {
			state.openStep = step;
			[1, 2, 3, 4, 5].forEach(function (number) {
				var body = root.querySelector('#step' + number + '_div_box');
				if (body) {
					body.classList.toggle('show', number === step);
				}
			});
		}

		function updateStepSummaries() {
			var usage = getUsage();
			var lensType = schema.lens_types[String(state.lenstype)] || schema.lens_types[state.lenstype];
			var lensColor = schema.lens_colors[String(state.lenstype_color)] || schema.lens_colors[state.lenstype_color];
			var lensIndex = schema.lens_indices[String(state.lensindex)] || schema.lens_indices[state.lensindex];
			var coating = schema.coatings[String(state.coating)] || schema.coatings[state.coating];
			root.querySelector('#step_1_cn').innerHTML = usage ? usage.short_label + '&nbsp;&nbsp;<span class="un_line">&nbsp;EDIT&nbsp;</span>' : '';
			if (state.usage === 20) {
				root.querySelector('#step_2_cn').innerHTML = '';
			} else if (state.usage === 2 && state.readers === 1 && Number(state.power || 0) > 0) {
				root.querySelector('#step_2_cn').innerHTML = 'Readers (+' + state.power + ')&nbsp;&nbsp;<span class="un_line">&nbsp;EDIT&nbsp;</span>';
			} else {
				root.querySelector('#step_2_cn').innerHTML = state.form.rx_name + '&nbsp;&nbsp;<span class="un_line">&nbsp;EDIT&nbsp;</span>';
			}
			root.querySelector('#step_3_cn').innerHTML = lensType ? lensType.label + (lensColor ? ' / ' + lensColor.label : '') + '&nbsp;&nbsp;<span class="un_line">&nbsp;EDIT&nbsp;</span>' : '';
			root.querySelector('#step_4_cn').innerHTML = lensIndex ? lensIndex.label + '&nbsp;&nbsp;<span class="un_line">&nbsp;EDIT&nbsp;</span>' : '';
			root.querySelector('#step_5_cn').innerHTML = coating ? coating.label + '&nbsp;&nbsp;<span class="un_line">&nbsp;EDIT&nbsp;</span>' : '';
		}

		function renderUsageStep() {
			var mount = root.querySelector('[data-step-list="1"]');
			mount.innerHTML = '';
			schema.usage_options.forEach(function (option) {
				var li = document.createElement('li');
				li.className = 'col-xs-6 col-sm-6 col-md-6 col-lg-4';
				var active = Number(state.usage) === Number(option.id);
				li.innerHTML = '<div id="step1_li_' + option.id + '" class="panel panel-default lens_key' + (active ? ' lens_k_choose' : '') + '" step="1" val="' + option.id + '">' +
					'<div class="panel-heading">' + option.label + '<span class="icon dripicons-question f-right mt-1">?</span></div>' +
					'<div class="panel-body fs14"><div class="step_div_info row"><div class="col-4">' + renderPhoto(usageImages[option.id], option.short_label.charAt(0)) + '</div><div class="col-8 pdnone"><div>' + option.description + '</div><div class="mt-10"><span class="lens_k_price">' + (option.id === 3 ? '<del>$18.75</del> <span class="mk-price">$15.99</span><span class="lensoff">15% OFF</span>' : option.id === 4 ? '$35.99' : option.id === 5 ? '<del>$59.99</del> <span class="mk-price">$38.99</span><span class="lensoff">35% OFF</span>' : 'FREE') + '</span></div></div></div></div></div>';
				li.querySelector('.lens_key').addEventListener('click', function () {
					state.usage = Number(option.id);
					state.readers = 0;
					state.power = '0';
					state.lenstype = 0;
					state.lenstype_color = 0;
					state.lensindex = 0;
					state.coating = 0;
					state.pdkey = 1;
					state.nearpd = 0;
					state.prism = 0;
					state.upgradePrompted = false;
					ensureValidSelections();
					setOpenStep(2);
					render();
				});
				mount.appendChild(li);
			});
		}

		function renderPowerBox() {
			var mount = root.querySelector('#power_box');
			var visible = state.usage === 2 && state.readers === 1;
			mount.style.display = visible ? '' : 'none';
			if (!visible) {
				mount.innerHTML = '';
				return;
			}
			mount.innerHTML = "<div class=\"poewr_t\"><div class=\"mb-10 fs14 fw600\">Select your readers' lens power</div><div class=\"mb-10\">Our high-quality Readers are ready-made glasses with an equal magnification power in both lenses.</div><div class=\"mb-10 mk-blue strength_btn\">What is my strength?</div><div id=\"strength_info\" class=\"mt-10\" style=\"display:none;\"><p class=\"mb5\">Ready-made readers provide equal magnification to both eyes.</p><p class=\"mb5\">Download the diopter chart, print at 100%, hold it 14 inches away, and read from top to bottom.</p></div></div><div class=\"mlr-power-values\"></div><div class=\"text-center mt-20\"><button id=\"power-sure\" class=\"btn theme-btn-w fs16\">Next</button></div>";
			var values = mount.querySelector('.mlr-power-values');
			schema.prescription_fields.power.options.forEach(function (option) {
				var value = option.value.replace('+', '');
				var button = document.createElement('span');
				button.className = 'power_v' + (state.power === value ? ' power_v_choose' : '');
				button.textContent = option.label;
				button.addEventListener('click', function () {
					state.power = value;
					render();
				});
				values.appendChild(button);
			});
			mount.querySelector('#power-sure').addEventListener('click', function () {
				setOpenStep(3);
			});
			mount.querySelector('.strength_btn').addEventListener('click', function () {
				var info = mount.querySelector('#strength_info');
				info.style.display = info.style.display === 'none' ? 'block' : 'none';
			});
		}

		function renderPrescriptionBox() {
			var mount = root.querySelector('#prescription_box');
			if (state.usage === 20) {
				mount.innerHTML = '<div class="mlr-empty-prescription">Non-prescription selected. This step is skipped.</div>';
				return;
			}
			var addDisabled = [1, 20].indexOf(state.usage) >= 0;
			var axisOdDisabled = Number(state.form.od_cyl || 0) === 0;
			var axisOsDisabled = Number(state.form.os_cyl || 0) === 0;
			var pdHtml = state.pdkey === 1
				? '<div class="mlr-pd-single"><label>PD</label><select data-field="pd">' + schema.prescription_fields.pd.options.map(function (option) { return createOption(option, state.form.pd); }).join('') + '</select></div>'
				: '<div class="mlr-pd-double"><label>Right PD</label><select data-field="od_pd">' + schema.prescription_fields.od_pd.options.map(function (option) { return createOption(option, state.form.od_pd); }).join('') + '</select><label>Left PD</label><select data-field="os_pd">' + schema.prescription_fields.os_pd.options.map(function (option) { return createOption(option, state.form.os_pd); }).join('') + '</select></div>';
			var nearPdHtml = state.nearpd ? '<div class="mlr-pd-single"><label>Near PD</label><select data-field="npd">' + schema.prescription_fields.npd.options.map(function (option) { return createOption(option, state.form.npd === '0' ? '46' : state.form.npd); }).join('') + '</select></div>' : '';
			var prismHtml = state.prism ? '<div class="mlr-prism-grid">' +
				['od_prismnum_v', 'os_prismnum_v', 'od_prismdir_v', 'os_prismdir_v', 'od_prismnum_h', 'os_prismnum_h', 'od_prismdir_h', 'os_prismdir_h'].map(function (field) {
					return '<label>' + field.replace(/_/g, ' ').toUpperCase() + '<select data-field="' + field + '">' + schema.prescription_fields[field].options.map(function (option) { return createOption(option, state.form[field]); }).join('') + '</select></label>';
				}).join('') + '</div>' : '';
			mount.innerHTML = '<div class="mlr-rx-mode"><label><input type="radio" name="rx_mode" value="rx"' + (state.readers === 0 ? ' checked' : '') + '> Enter your prescription</label>' +
				(state.usage === 2 ? '<label><input type="radio" name="rx_mode" value="readers"' + (state.readers === 1 ? ' checked' : '') + '> For Readers: just select a lens power</label>' : '') + '</div>' +
				'<div class="pres_sr_box"><ul><li class="sr-title"><div>OD<span>(Right eye)</span></div><div>OS<span>(Left eye)</span></div></li></ul></div>' +
				'<div class="mlr-rx-table">' +
				buildPairSelect('Sphere(SPH)', 'od_sph', 'os_sph', false, false) +
				buildPairSelect('Cylinder(CYL)', 'od_cyl', 'os_cyl', false, false) +
				buildPairSelect('Axis', 'od_axis', 'os_axis', axisOdDisabled, axisOsDisabled) +
				buildPairSelect('ADD(Near Addition)', 'od_add', 'os_add', addDisabled, addDisabled) +
				'</div>' +
				'<div class="mlr-upload-copy">If you are not sure how to enter the prescription, you can upload the picture of the prescription, and we will complete the prescription information for you.</div>' +
				'<div class="mlr-pd-box">' + pdHtml + nearPdHtml + '</div>' +
				'<div class="mlr-toggle-line"><label><input type="checkbox" data-toggle="pd"' + (state.pdkey === 2 ? ' checked' : '') + '> Two PD numbers</label><label><input type="checkbox" data-toggle="nearpd"' + (state.nearpd === 1 ? ' checked' : '') + '> Near PD</label><label><input type="checkbox" data-toggle="prism"' + (state.prism === 1 ? ' checked' : '') + '> Add Prism +$9.95</label></div>' +
				prismHtml +
				'<h6>Comments:</h6><textarea data-field="lens_comment">' + (state.form.lens_comment || '') + '</textarea>' +
				'<div class="mlr-rx-name">Save prescription As:<input type="text" data-field="rx_name" value="' + state.form.rx_name + '"></div>' +
				'<div class="text-center mt-20"><button class="btn theme-btn-w fs16" data-next-step="3">SUBMIT PRESCRIPTION</button></div>';

			mount.querySelectorAll('select[data-field], textarea[data-field], input[type="text"][data-field]').forEach(function (input) {
				input.addEventListener('change', function () {
					state.form[input.getAttribute('data-field')] = input.value;
					ensureValidSelections();
					render();
				});
			});
			mount.querySelectorAll('input[name="rx_mode"]').forEach(function (input) {
				input.addEventListener('change', function () {
					state.readers = input.value === 'readers' ? 1 : 0;
					if (state.readers === 0) {
						state.power = '0';
					}
					render();
				});
			});
			mount.querySelectorAll('input[data-toggle]').forEach(function (input) {
				input.addEventListener('change', function () {
					var type = input.getAttribute('data-toggle');
					if (type === 'pd') {
						state.pdkey = input.checked ? 2 : 1;
					}
					if (type === 'nearpd') {
						state.nearpd = input.checked ? 1 : 0;
					}
					if (type === 'prism') {
						state.prism = input.checked ? 1 : 0;
					}
					render();
				});
			});
			mount.querySelector('[data-next-step="3"]').addEventListener('click', function () {
				if (validateStep2()) {
					setOpenStep(3);
				}
			});
		}

		function buildPairSelect(label, odField, osField, odDisabled, osDisabled) {
			return '<div class="mlr-rx-row"><div class="sr-name-title"><span>' + label + '</span></div>' +
				'<label><select data-field="' + odField + '"' + (odDisabled ? ' disabled' : '') + '>' + schema.prescription_fields[odField].options.map(function (option) { return createOption(option, state.form[odField]); }).join('') + '</select></label>' +
				'<label><select data-field="' + osField + '"' + (osDisabled ? ' disabled' : '') + '>' + schema.prescription_fields[osField].options.map(function (option) { return createOption(option, state.form[osField]); }).join('') + '</select></label></div>';
		}

		function renderLensTypeStep() {
			var mount = root.querySelector('[data-step-list="3"]');
			mount.innerHTML = '';
			Object.keys(schema.lens_types).forEach(function (key) {
				var option = schema.lens_types[key];
				if (option.dom_hidden) {
					return;
				}
				var allowed = getAllowedLensTypes().indexOf(Number(option.id)) >= 0;
				var li = document.createElement('li');
				li.className = 'col-xs-6 col-sm-6 col-md-6 col-lg-4';
				li.innerHTML = '<div id="step3_li_' + option.id + '" class="panel panel-default lens_key' + (Number(state.lenstype) === Number(option.id) ? ' lens_k_choose' : '') + (allowed ? '' : ' disable_step_panel') + '" step="3" val="' + option.id + '">' +
					'<div class="panel-heading">' + option.label + '<span class="icon dripicons-question f-right mt-1">?</span></div>' +
					'<div class="panel-body fs14"><div class="step_div_info row"><div class="col-4">' + renderPhoto(lensTypeImages[option.id], option.label.charAt(0)) + '</div><div class="col-8 pdnone"><div>' + option.description + '</div><div class="mt-10"><span class="lens_k_price">' + (getLensTypePrice(option.id) ? money(getLensTypePrice(option.id)) : 'FREE') + '</span></div></div></div></div></div>';
				var card = li.querySelector('.lens_key');
				if (allowed) {
					card.addEventListener('click', function () {
						state.lenstype = Number(option.id);
						state.lenstype_color = 0;
						ensureValidSelections();
						setOpenStep(4);
						render();
					});
				}
				mount.appendChild(li);
				if (Number(state.lenstype) === Number(option.id) && option.requires_color) {
					var colorLi = document.createElement('li');
					colorLi.className = 'col-12';
					var colors = getLensColorOptions(option.id).map(function (color) {
						return '<button type="button" class="mlr-color-choice' + (Number(state.lenstype_color) === Number(color.id) ? ' active' : '') + '" data-color="' + color.id + '">' + color.label + '</button>';
					}).join('');
					colorLi.innerHTML = '<div class="mlr-color-wrap">' + colors + '</div>';
					mount.appendChild(colorLi);
					colorLi.querySelectorAll('[data-color]').forEach(function (button) {
						button.addEventListener('click', function () {
							state.lenstype_color = Number(button.getAttribute('data-color'));
							ensureValidSelections();
							render();
						});
					});
				}
			});
		}

		function renderLensIndexStep() {
			var mount = root.querySelector('[data-step-list="4"]');
			var rule = getAllowedLensIndices();
			var recommended = getRecommendedIndex();
			mount.innerHTML = '';
			Object.keys(schema.lens_indices).forEach(function (key) {
				var option = schema.lens_indices[key];
				var allowed = rule.allowed.indexOf(Number(option.id)) >= 0;
				var disabled = !allowed || rule.disabled.indexOf(Number(option.id)) >= 0;
				var li = document.createElement('li');
				li.className = 'col-6 col-lg-6';
				li.innerHTML = '<div id="step4_li_' + option.id + '" class="panel panel-default step4_panel lens_key' + (Number(state.lensindex) === Number(option.id) ? ' lens_k_choose' : '') + (disabled ? ' disable_step_panel' : '') + '" step="4" val="' + option.id + '">' +
					'<div class="panel-heading">' + option.label + (recommended === Number(option.id) ? '<span class="recommended-icon">Recommended</span>' : '') + '<span class="icon dripicons-question f-right mt-1">?</span></div>' +
					'<div class="panel-body fs14"><div class="step_div_info row"><div class="col-4">' + renderPhoto(lensIndexImages[option.id], option.label.charAt(0)) + '</div><div class="col-8 pdnone"><div>Available for the current prescription flow.</div><div class="mt-10"><span class="lens_k_price">' + money(getLensIndexPrice(option.id)) + '</span></div></div></div></div></div>';
				if (!disabled) {
					li.querySelector('.lens_key').addEventListener('click', function () {
						state.lensindex = Number(option.id);
						setOpenStep(5);
						render();
					});
				}
				mount.appendChild(li);
			});
		}

		function renderCoatingStep() {
			var mount = root.querySelector('[data-step-list="5"]');
			mount.innerHTML = '';
			Object.keys(schema.coatings).forEach(function (key) {
				var option = schema.coatings[key];
				var li = document.createElement('li');
				li.className = 'col-xs-6 col-sm-6 col-md-6 col-lg-4';
				li.innerHTML = '<div id="step5_li_' + option.id + '" class="panel panel-default lens_key' + (Number(state.coating) === Number(option.id) ? ' lens_k_choose' : '') + '" step="5" val="' + option.id + '">' +
					'<div class="panel-heading">' + option.label + '<span class="icon dripicons-question f-right mt-1">?</span></div>' +
					'<div class="panel-body fs14"><div class="step_div_info row"><div class="col-4">' + renderPhoto(coatingImages[option.id], option.label.charAt(0)) + '</div><div class="col-8 pdnone"><div>' + option.description + '</div><div class="mt-10"><span class="lens_k_price">' + (getCoatingPrice(option.id) ? money(getCoatingPrice(option.id)) : 'FREE') + '</span></div></div></div></div></div>';
				li.querySelector('.lens_key').addEventListener('click', function () {
					state.coating = Number(option.id);
					render();
				});
				mount.appendChild(li);
			});
		}

		function renderSummary() {
			var usage = getUsage();
			var lensType = schema.lens_types[String(state.lenstype)] || schema.lens_types[state.lenstype];
			var lensColor = schema.lens_colors[String(state.lenstype_color)] || schema.lens_colors[state.lenstype_color];
			var lensIndex = schema.lens_indices[String(state.lensindex)] || schema.lens_indices[state.lensindex];
			var coating = schema.coatings[String(state.coating)] || schema.coatings[state.coating];
			var blueButton = root.querySelector('.bluelight-btn');
			var blueButtonLabel = root.querySelector('#deta_lenst_bluelight');
			root.querySelector('#deta_usage').textContent = usage ? usage.short_label : '';
			root.querySelector('#deta_usage_p').textContent = '';
			root.querySelector('#deta_lenst_prism').textContent = state.prism ? 'YES' : 'NONE';
			root.querySelector('#deta_lenst_prism_p').textContent = state.prism ? money(getPrismPrice()) : '';
			root.querySelector('#deta_lenst_type').textContent = lensType ? lensType.label + (lensColor ? ' / ' + lensColor.label : '') : '';
			root.querySelector('#deta_lenst_type_p').textContent = lensType ? money(getLensTypePrice(state.lenstype)) : '';
			root.querySelector('#deta_lenst_index').textContent = lensIndex ? lensIndex.label : '';
			root.querySelector('#deta_lenst_index_p').textContent = lensIndex ? money(getLensIndexPrice(state.lensindex)) : '';
			root.querySelector('#deta_lenst_coatinc').textContent = coating ? coating.label : '';
			root.querySelector('#deta_lenst_coatinc_p').textContent = coating ? (getCoatingPrice(state.coating) ? money(getCoatingPrice(state.coating)) : 'FREE') : '';
			blueButtonLabel.textContent = state.bluelight ? 'YES' : 'NO';
			root.querySelector('#deta_lenst_bluelight_p').textContent = state.bluelight ? money(getBlueLightPrice()) : '';
			root.querySelector('#data_attr_2').style.display = state.prism ? 'block' : 'none';
			root.querySelector('#data_attr_3').style.display = lensType ? 'block' : 'none';
			root.querySelector('#data_attr_4').style.display = lensIndex ? 'block' : 'none';
			root.querySelector('#data_attr_5').style.display = coating ? 'block' : 'none';
			root.querySelector('#lens_price').textContent = getLensTotal().toFixed(2);
			root.querySelector('#total').textContent = getGrandTotal().toFixed(2);
			blueButton.classList.toggle('bluelight-check', state.bluelight);
			blueButton.classList.toggle('is-disabled', bluelightLocked());
			blueButton.querySelector('.lock_tips').style.display = bluelightLocked() ? 'block' : 'none';
		}

		function renderPayload() {
			if (!payloadPreview) {
				return;
			}

			payloadPreview.textContent = state.payload ? JSON.stringify(state.payload, null, 2) : 'Waiting for payload...';
		}

		function renderProductSummary() {
			var image = root.querySelector('#lens_img_v');
			var title = root.querySelector('#lens_goods_title');
			var size = root.querySelector('#lens_size_v');
			var measurements = root.querySelector('#lens_measurements_v');
			var color = root.querySelector('#lens_color_v');
			var framePrice = root.querySelector('#frame_price');

			if (title) {
				title.textContent = schema.product.describe || '';
			}

			if (size) {
				size.textContent = schema.product.size || '';
			}

			if (measurements) {
				measurements.textContent = schema.product.measurements || '';
			}

			if (color) {
				color.textContent = schema.product.color_label || '';
			}

			if (framePrice) {
				framePrice.textContent = Number(schema.product.frame_price || 0).toFixed(2);
			}

			if (image && image.tagName === 'IMG' && schema.product.image_url) {
				image.src = schema.product.image_url;
			}
		}

		function render() {
			ensureValidSelections();
			renderUsageStep();
			renderPowerBox();
			renderPrescriptionBox();
			renderLensTypeStep();
			renderLensIndexStep();
			renderCoatingStep();
			renderProductSummary();
			updateStepSummaries();
			renderSummary();
			renderPayload();
			setOpenStep(state.openStep);
		}

		function openOverlay() {
			container.hidden = false;
			document.body.classList.add('mlr-open-body');
			render();
		}

		function closeOverlay() {
			container.hidden = true;
			document.body.classList.remove('mlr-open-body');
			root.querySelector('#lensbox_left').style.display = '';
			root.querySelector('#lensbox_right').classList.remove('col-full');
			editAgain.style.display = 'none';
		}

		function maybeHandleProgressiveUpgrade() {
			if (state.usage !== 4 || state.upgradePrompted) {
				return Promise.resolve();
			}
			var premiumLensTypeTable = schema.pricing.lenstype_price['5'] || schema.pricing.lenstype_price[5];
			var premiumIndexTable = schema.pricing.lensindex_price['5'] || schema.pricing.lensindex_price[5];
			var premiumLensType = premiumLensTypeTable[String(state.lenstype)] || premiumLensTypeTable[state.lenstype];
			var premiumIndex = premiumIndexTable[String(state.lensindex)] || premiumIndexTable[state.lensindex];
			var upgradeDelta = (premiumLensType ? Number(premiumLensType[4]) : getLensTypePrice(state.lenstype)) - getLensTypePrice(state.lenstype) + (premiumIndex ? Number(premiumIndex[1]) : getLensIndexPrice(state.lensindex)) - getLensIndexPrice(state.lensindex);
			upgradeCopy.textContent = 'Extra lens cost: ' + money(upgradeDelta) + '.';
			upgradeModal.hidden = false;
			return new Promise(function (resolve) {
				function cleanup() {
					upgradeModal.hidden = true;
					upgradeAccept.removeEventListener('click', accept);
					upgradeSkip.removeEventListener('click', skip);
				}
				function accept() {
					state.usage = 5;
					state.upgradePrompted = true;
					cleanup();
					render();
					resolve();
				}
				function skip() {
					state.upgradePrompted = true;
					cleanup();
					resolve();
				}
				upgradeAccept.addEventListener('click', accept);
				upgradeSkip.addEventListener('click', skip);
			});
		}

		async function simulateSubmit() {
			if (!validateBeforeSubmit()) {
				return;
			}
			await maybeHandleProgressiveUpgrade();
			if (!validateBeforeSubmit()) {
				return;
			}
			setStatus('Generating lens payload...');
			var body = new window.URLSearchParams();
			body.append('action', 'muukal_lens_replica_build_payload');
			body.append('nonce', config.nonce);
			body.append('state', JSON.stringify({
				product: {
					id: schema.product.id,
					color_id: schema.product.color_id,
					frame_price: schema.product.frame_price
				},
				usage: state.usage,
				lenstype: state.lenstype,
				lenstype_color: state.lenstype_color,
				lensindex: state.lensindex,
				coating: state.coating,
				pdkey: state.pdkey,
				nearpd: state.nearpd,
				prism: state.prism,
				bluelight: state.bluelight,
				readers: state.readers,
				power: state.power,
				total: getGrandTotal(),
				form: state.form
			}));
			var response = await window.fetch(config.ajaxUrl, {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
				body: body.toString(),
				credentials: 'same-origin'
			});
			var result = await response.json();
			if (!response.ok || !result.success) {
				setStatus('Payload generation failed.');
				return;
			}
			state.payload = result.data.payload;
			renderPayload();
			root.querySelector('#lensbox_left').style.display = 'none';
			root.querySelector('#lensbox_right').classList.add('col-full');
			editAgain.style.display = 'block';
			setStatus('Lens payload generated.');
		}

		openButton.addEventListener('click', openOverlay);
		closeButtons.forEach(function (button) {
			button.addEventListener('click', closeOverlay);
		});
		root.querySelectorAll('.mlr-step-toggle').forEach(function (toggle) {
			toggle.addEventListener('click', function () {
				setOpenStep(Number(toggle.getAttribute('data-step')));
				render();
			});
		});
		root.querySelector('.bluelight-btn').addEventListener('click', function () {
			if (bluelightLocked()) {
				setStatus('Blue light add-on is locked for this combination.');
				return;
			}
			state.bluelight = !state.bluelight;
			render();
		});
		submitButton.addEventListener('click', function () {
			simulateSubmit().catch(function () {
				setStatus('Unexpected error while building payload.');
			});
		});
		editAgain.addEventListener('click', function () {
			root.querySelector('#lensbox_left').style.display = '';
			root.querySelector('#lensbox_right').classList.remove('col-full');
			editAgain.style.display = 'none';
			setOpenStep(1);
			render();
		});

		if (copyButton) {
			copyButton.addEventListener('click', function () {
				if (!state.payload) {
					return;
				}
				window.navigator.clipboard.writeText(JSON.stringify(state.payload, null, 2));
				setStatus('Payload copied to clipboard.');
			});
		}

		document.addEventListener('muukal:productColorChanged', function (event) {
			var detail = event.detail || {};

			if (String(detail.productId || '') !== String(schema.product.id || '')) {
				return;
			}

			if (detail.colorId) {
				schema.product.color_id = detail.colorId;
			}

			if (detail.colorLabel) {
				schema.product.color_label = detail.colorLabel;
			}

			if (detail.price) {
				schema.product.frame_price = Number(detail.price);
			}

			if (detail.imageUrl) {
				schema.product.image_url = detail.imageUrl;
			}

			render();
		});
		render();
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.mlr-app').forEach(init);
	});
})();
