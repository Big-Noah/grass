(function () {
	'use strict';

	var config = window.muukalLensReplicaConfig || {};
	var schema = config.config || {};

	function money(value) {
		return '$' + Number(value || 0).toFixed(2);
	}

	function absMax(values) {
		return Math.max.apply(null, values.map(function (value) {
			return Math.abs(Number(value || 0));
		}));
	}

	function createOption(value, label) {
		var option = document.createElement('option');
		option.value = value;
		option.textContent = label;
		return option;
	}

	function init(root) {
		var openButton = root.querySelector('.mlr-open');
		var overlay = root.querySelector('.mlr-overlay');
		var closeButtons = root.querySelectorAll('[data-close]');
		var stepsMount = root.querySelector('.mlr-steps');
		var summaryLines = root.querySelector('.mlr-summary-lines');
		var addonBox = root.querySelector('.mlr-addon');
		var totalsBox = root.querySelector('.mlr-totals');
		var status = root.querySelector('.mlr-status');
		var payloadPreview = root.querySelector('.mlr-payload-preview');
		var copyButton = root.querySelector('.mlr-copy-payload');
		var submitButton = root.querySelector('.mlr-submit');
		var upgradeModal = root.querySelector('.mlr-upgrade-modal');
		var upgradeCopy = root.querySelector('.mlr-upgrade-copy');
		var upgradeAccept = root.querySelector('.mlr-upgrade-accept');
		var upgradeSkip = root.querySelector('.mlr-upgrade-skip');
		var defaultForm = {
			od_sph: '-1.25', os_sph: '-1.00', od_cyl: '-0.50', os_cyl: '-0.25', od_axis: '90', os_axis: '80',
			od_add: '0', os_add: '0', pd: '63', od_pd: '0', os_pd: '0', npd: '0', birth_year: '0',
			od_prismnum_v: '0', os_prismnum_v: '0', od_prismdir_v: '0', os_prismdir_v: '0',
			od_prismnum_h: '0', os_prismnum_h: '0', od_prismdir_h: '0', os_prismdir_h: '0',
			lens_comment: '', rx_name: 'Replica Test Rx'
		};
		var state = {
			usage: 1, readers: 0, power: '0', lenstype: 0, lenstype_color: 0, lensindex: 0, coating: 0,
			pdkey: 1, nearpd: 0, prism: 0, bluelight: false, form: Object.assign({}, defaultForm), payload: null,
			upgradePrompted: false
		};

		function setStatus(message) {
			status.textContent = message || '';
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

		function getLensColorOptions(lenstypeId) {
			return (schema.lens_type_color_map[String(lenstypeId)] || schema.lens_type_color_map[lenstypeId] || []).map(function (colorId) {
				return schema.lens_colors[String(colorId)] || schema.lens_colors[colorId];
			});
		}

		function bluelightLocked() {
			return state.usage === 3 || state.lenstype === 7 || Number(state.lenstype_color) === 52;
		}

		function syncBluelightLock() {
			if (bluelightLocked()) {
				state.bluelight = false;
			}
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

		function getRecommendedIndex() {
			var odSph = Math.abs(Number(state.form.od_sph || 0));
			var osSph = Math.abs(Number(state.form.os_sph || 0));
			var odCyl = Math.abs(Number(state.form.od_cyl || 0));
			var osCyl = Math.abs(Number(state.form.os_cyl || 0));
			var odAdd = Math.abs(Number(state.form.od_sph || 0) + Number(state.form.od_add || 0));
			var osAdd = Math.abs(Number(state.form.os_sph || 0) + Number(state.form.os_add || 0));
			var maxBase = Math.max(odSph, osSph, odCyl, osCyl);
			var maxAdd = Math.max(odAdd, osAdd);

			if (state.usage === 1 || (state.usage === 2 && state.readers !== 1)) {
				if (maxBase > 8) { return 5; }
				if (maxBase > 6) { return 4; }
				if (maxBase > 2.25) { return 3; }
			}
			if (state.usage === 2 && state.readers === 1) {
				var power = Number(state.power || 0);
				if (power > 6.25) { return 5; }
				if (power > 3.5) { return 4; }
				if (power > 2) { return 3; }
			}
			if (state.usage === 3 || state.usage === 4) {
				if (Math.max(maxBase, maxAdd) > 5.75) { return 4; }
				if (Math.max(maxBase, maxAdd) > 2.25) { return 3; }
			}
			if (state.usage === 5) {
				if (Math.max(maxBase, maxAdd) > 7.25) { return 5; }
				if (Math.max(maxBase, maxAdd) > 5.25) { return 4; }
				if (Math.max(maxBase, maxAdd) > 2.25) { return 3; }
			}
			return 0;
		}

		function getLensTypePrice(lenstypeId) {
			var row = getUsagePriceTable()[String(lenstypeId)] || getUsagePriceTable()[lenstypeId];
			return row ? Number(row[4]) : 0;
		}

		function getLensIndexPrice(indexId) {
			var row = getIndexPriceTable()[String(indexId)] || getIndexPriceTable()[indexId];
			return row ? Number(row[1]) : 0;
		}

		function getCoatingPrice(coatingId) {
			var row = schema.pricing.coating_price[String(coatingId)] || schema.pricing.coating_price[coatingId];
			return row ? Number(row[1]) : 0;
		}

		function getPrismPrice() {
			if (!state.prism) { return 0; }
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

		function validateStep2() {
			if (state.usage === 20) { return true; }
			if (state.usage === 2 && state.readers === 1) { return Number(state.power || 0) > 0; }
			var hasRx = Number(state.form.od_sph || 0) !== 0 || Number(state.form.os_sph || 0) !== 0 || Number(state.form.od_cyl || 0) !== 0 || Number(state.form.os_cyl || 0) !== 0 || Number(state.form.od_add || 0) !== 0 || Number(state.form.os_add || 0) !== 0;
			if (!hasRx) { setStatus('Prescription has no value. Use non-prescription if you do not need Rx lenses.'); return false; }
			if ((Number(state.form.od_cyl || 0) !== 0 && !Number(state.form.od_axis || 0)) || (Number(state.form.os_cyl || 0) !== 0 && !Number(state.form.os_axis || 0))) {
				setStatus('CYL requires AXIS for the same eye.'); return false;
			}
			if (state.pdkey === 2 && (!Number(state.form.od_pd || 0) || !Number(state.form.os_pd || 0))) {
				setStatus('Two-PD mode requires right and left PD.'); return false;
			}
			if ([3, 4, 5].indexOf(state.usage) >= 0 && (!Number(state.form.od_add || 0) || !Number(state.form.os_add || 0))) {
				setStatus('Bifocal and progressive flows require ADD values.'); return false;
			}
			if (state.prism) {
				var hasPrismNumber = Number(state.form.od_prismnum_v || 0) || Number(state.form.os_prismnum_v || 0) || Number(state.form.od_prismnum_h || 0) || Number(state.form.os_prismnum_h || 0);
				var hasPrismDirection = state.form.od_prismdir_v !== '0' || state.form.os_prismdir_v !== '0' || state.form.od_prismdir_h !== '0' || state.form.os_prismdir_h !== '0';
				if (!hasPrismNumber || !hasPrismDirection) { setStatus('Prism mode requires number and direction fields.'); return false; }
			}
			if ((state.form.lens_comment || '').length > 200) { setStatus('Comment length must stay within 200 characters.'); return false; }
			return true;
		}

		function validateBeforeSubmit() {
			if (!state.usage) { setStatus('Choose a usage first.'); return false; }
			if (!validateStep2()) { return false; }
			if (!state.lenstype) { setStatus('Choose a lens type.'); return false; }
			if ((schema.lens_types[String(state.lenstype)] || {}).requires_color && !state.lenstype_color) { setStatus('This lens type requires a color selection.'); return false; }
			if (!state.lensindex) { setStatus('Choose a lens thickness.'); return false; }
			if (!state.coating) { setStatus('Choose a coating.'); return false; }
			return true;
		}

		function renderSummary() {
			var usage = schema.usage_options.find(function (item) { return Number(item.id) === Number(state.usage); });
			var lensType = schema.lens_types[String(state.lenstype)] || schema.lens_types[state.lenstype];
			var lensColor = schema.lens_colors[String(state.lenstype_color)] || schema.lens_colors[state.lenstype_color];
			var lensIndex = schema.lens_indices[String(state.lensindex)] || schema.lens_indices[state.lensindex];
			var coating = schema.coatings[String(state.coating)] || schema.coatings[state.coating];
			var lines = [];
			lines.push('<div class=\"mlr-line\"><span>Usage</span><strong>' + (usage ? usage.short_label : 'Not selected') + '</strong></div>');
			lines.push('<div class=\"mlr-line\"><span>Lens Type</span><strong>' + (lensType ? lensType.label : 'Not selected') + '</strong></div>');
			if (lensColor) { lines.push('<div class=\"mlr-line\"><span>Lens Color</span><strong>' + lensColor.label + '</strong></div>'); }
			lines.push('<div class=\"mlr-line\"><span>Thickness</span><strong>' + (lensIndex ? lensIndex.label : 'Not selected') + '</strong></div>');
			lines.push('<div class=\"mlr-line\"><span>Coating</span><strong>' + (coating ? coating.label : 'Not selected') + '</strong></div>');
			if (state.readers === 1 && state.usage === 2) { lines.push('<div class=\"mlr-line\"><span>Reader Power</span><strong>+' + state.power + '</strong></div>'); }
			summaryLines.innerHTML = lines.join('');
			addonBox.innerHTML = '<div class=\"mlr-addon-row\"><span>Blue Light Add-on</span><strong>' + (state.bluelight ? money(getBlueLightPrice()) : 'No') + '</strong></div><div class=\"mlr-addon-row\"><span>Prism</span><strong>' + (state.prism ? money(getPrismPrice()) : 'No') + '</strong></div>';
			totalsBox.innerHTML = '<div class=\"mlr-total-row\"><span>Frame</span><strong>' + money(schema.product.frame_price) + '</strong></div><div class=\"mlr-total-row\"><span>Lens</span><strong>' + money(getLensTotal()) + '</strong></div><div class=\"mlr-total-row is-grand\"><span>Total</span><strong>' + money(getGrandTotal()) + '</strong></div>';
		}

		function buildSelect(id, options, value) {
			var select = document.createElement('select');
			options.forEach(function (option) { select.appendChild(createOption(option.value, option.label)); });
			select.value = value;
			select.addEventListener('change', function () {
				state.form[id] = select.value;
				if (id === 'od_cyl' && Number(select.value) === 0) { state.form.od_axis = ''; }
				if (id === 'os_cyl' && Number(select.value) === 0) { state.form.os_axis = ''; }
				ensureValidSelections();
				render();
			});
			return select;
		}

		function renderUsageStep() {
			var card = document.createElement('section');
			card.className = 'mlr-step is-open';
			card.innerHTML = '<header><p>Step 1</p><h3>What do you use your glasses for?</h3></header>';
			var options = document.createElement('div');
			options.className = 'mlr-option-grid';
			schema.usage_options.forEach(function (option) {
				var button = document.createElement('button');
				button.type = 'button';
				button.className = 'mlr-option-card' + (Number(state.usage) === Number(option.id) ? ' is-active' : '');
				button.innerHTML = '<strong>' + option.label + '</strong><span>' + option.description + '</span>';
				button.addEventListener('click', function () {
					state.usage = Number(option.id); state.readers = 0; state.power = '0'; state.lenstype = 0; state.lenstype_color = 0; state.lensindex = 0; state.coating = 0; state.pdkey = 1; state.nearpd = 0; state.prism = 0; state.upgradePrompted = false; ensureValidSelections(); render();
				});
				options.appendChild(button);
			});
			card.appendChild(options);
			return card;
		}

		function renderPrescriptionControls() {
			var wrap = document.createElement('div');
			wrap.className = 'mlr-form';
			if (state.usage === 2) {
				var choice = document.createElement('div');
				choice.className = 'mlr-choice-row';
				choice.innerHTML = '<label><input type=\"radio\" name=\"rx_mode\" value=\"rx\"' + (state.readers === 0 ? ' checked' : '') + '> Enter your prescription</label><label><input type=\"radio\" name=\"rx_mode\" value=\"readers\"' + (state.readers === 1 ? ' checked' : '') + '> For Readers: just select a lens power</label>';
				choice.addEventListener('change', function (event) { state.readers = event.target.value === 'readers' ? 1 : 0; if (state.readers === 0) { state.power = '0'; } ensureValidSelections(); render(); });
				wrap.appendChild(choice);
			}
			if (state.usage === 2 && state.readers === 1) {
				var powers = document.createElement('div');
				powers.className = 'mlr-power-grid';
				schema.prescription_fields.power.options.forEach(function (option) {
					var button = document.createElement('button');
					button.type = 'button';
					button.className = 'mlr-chip' + (state.power === option.value.replace('+', '') ? ' is-active' : '');
					button.textContent = option.label;
					button.addEventListener('click', function () { state.power = option.value.replace('+', ''); ensureValidSelections(); render(); });
					powers.appendChild(button);
				});
				wrap.appendChild(powers);
				return wrap;
			}
			var formGrid = document.createElement('div');
			formGrid.className = 'mlr-rx-grid';
			[
				{ label: 'OD SPH', id: 'od_sph' }, { label: 'OS SPH', id: 'os_sph' }, { label: 'OD CYL', id: 'od_cyl' }, { label: 'OS CYL', id: 'os_cyl' },
				{ label: 'OD AXIS', id: 'od_axis' }, { label: 'OS AXIS', id: 'os_axis' }, { label: 'OD ADD', id: 'od_add' }, { label: 'OS ADD', id: 'os_add' }
			].forEach(function (field) {
				var label = document.createElement('label'); label.className = 'mlr-field'; label.innerHTML = '<span>' + field.label + '</span>';
				var select = buildSelect(field.id, schema.prescription_fields[field.id].options, state.form[field.id]);
				if ((field.id === 'od_axis' && Number(state.form.od_cyl || 0) === 0) || (field.id === 'os_axis' && Number(state.form.os_cyl || 0) === 0)) { select.disabled = true; }
				if ((field.id === 'od_add' || field.id === 'os_add') && [1, 20].indexOf(state.usage) >= 0) { select.disabled = true; select.value = '0'; }
				label.appendChild(select); formGrid.appendChild(label);
			});
			wrap.appendChild(formGrid);
			var toggles = document.createElement('div');
			toggles.className = 'mlr-toggle-row';
			toggles.innerHTML = '<label><input type=\"checkbox\" ' + (state.pdkey === 2 ? 'checked' : '') + '> Two PD values</label><label><input type=\"checkbox\" data-nearpd=\"1\" ' + (state.nearpd === 1 ? 'checked' : '') + '> Near PD</label><label><input type=\"checkbox\" data-prism=\"1\" ' + (state.prism === 1 ? 'checked' : '') + '> Prism</label>';
			var toggleInputs = toggles.querySelectorAll('input');
			toggleInputs[0].addEventListener('change', function () { state.pdkey = toggleInputs[0].checked ? 2 : 1; render(); });
			toggleInputs[1].addEventListener('change', function () { state.nearpd = toggleInputs[1].checked ? 1 : 0; render(); });
			toggleInputs[2].addEventListener('change', function () { state.prism = toggleInputs[2].checked ? 1 : 0; render(); });
			wrap.appendChild(toggles);
			var pdGrid = document.createElement('div');
			pdGrid.className = 'mlr-pd-grid';
			if (state.pdkey === 1) {
				var pdLabel = document.createElement('label'); pdLabel.className = 'mlr-field'; pdLabel.innerHTML = '<span>PD</span>'; pdLabel.appendChild(buildSelect('pd', schema.prescription_fields.pd.options, state.form.pd)); pdGrid.appendChild(pdLabel);
			} else {
				['od_pd', 'os_pd'].forEach(function (fieldId) { var pdField = document.createElement('label'); pdField.className = 'mlr-field'; pdField.innerHTML = '<span>' + (fieldId === 'od_pd' ? 'Right PD' : 'Left PD') + '</span>'; pdField.appendChild(buildSelect(fieldId, schema.prescription_fields[fieldId].options, state.form[fieldId])); pdGrid.appendChild(pdField); });
			}
			if (state.nearpd === 1) { var npdField = document.createElement('label'); npdField.className = 'mlr-field'; npdField.innerHTML = '<span>Near PD</span>'; npdField.appendChild(buildSelect('npd', schema.prescription_fields.npd.options, state.form.npd === '0' ? '46' : state.form.npd)); pdGrid.appendChild(npdField); }
			wrap.appendChild(pdGrid);
			if (state.prism === 1) {
				var prismGrid = document.createElement('div'); prismGrid.className = 'mlr-rx-grid';
				[{ label: 'OD Prism V', id: 'od_prismnum_v' }, { label: 'OS Prism V', id: 'os_prismnum_v' }, { label: 'OD Base V', id: 'od_prismdir_v' }, { label: 'OS Base V', id: 'os_prismdir_v' }, { label: 'OD Prism H', id: 'od_prismnum_h' }, { label: 'OS Prism H', id: 'os_prismnum_h' }, { label: 'OD Base H', id: 'od_prismdir_h' }, { label: 'OS Base H', id: 'os_prismdir_h' }].forEach(function (field) { var prismField = document.createElement('label'); prismField.className = 'mlr-field'; prismField.innerHTML = '<span>' + field.label + '</span>'; prismField.appendChild(buildSelect(field.id, schema.prescription_fields[field.id].options, state.form[field.id])); prismGrid.appendChild(prismField); });
				wrap.appendChild(prismGrid);
			}
			return wrap;
		}

		function renderPrescriptionStep() {
			var card = document.createElement('section'); card.className = 'mlr-step is-open'; card.innerHTML = '<header><p>Step 2</p><h3>Enter your prescription</h3></header>'; card.appendChild(renderPrescriptionControls()); return card;
		}

		function renderLensTypeStep() {
			var card = document.createElement('section'); card.className = 'mlr-step is-open'; card.innerHTML = '<header><p>Step 3</p><h3>Lens type</h3></header>';
			var wrap = document.createElement('div'); wrap.className = 'mlr-option-grid';
			Object.keys(schema.lens_types).forEach(function (key) {
				var option = schema.lens_types[key]; var allowed = getAllowedLensTypes().indexOf(Number(option.id)) >= 0;
				if (option.dom_hidden) { return; }
				var button = document.createElement('button'); button.type = 'button'; button.className = 'mlr-option-card' + (Number(state.lenstype) === Number(option.id) ? ' is-active' : '') + (!allowed ? ' is-disabled' : ''); button.disabled = !allowed;
				button.innerHTML = '<strong>' + option.label + '</strong><span>' + option.description + '</span><em>' + money(getLensTypePrice(option.id)) + '</em>';
				button.addEventListener('click', function () { state.lenstype = Number(option.id); state.lenstype_color = 0; syncBluelightLock(); ensureValidSelections(); render(); });
				wrap.appendChild(button);
				if (Number(state.lenstype) === Number(option.id) && option.requires_color) {
					var colors = document.createElement('div'); colors.className = 'mlr-color-grid';
					getLensColorOptions(option.id).forEach(function (color) { var chip = document.createElement('button'); chip.type = 'button'; chip.className = 'mlr-color-chip' + (Number(state.lenstype_color) === Number(color.id) ? ' is-active' : ''); chip.textContent = color.label; chip.addEventListener('click', function () { state.lenstype_color = Number(color.id); syncBluelightLock(); ensureValidSelections(); render(); }); colors.appendChild(chip); });
					wrap.appendChild(colors);
				}
			});
			card.appendChild(wrap); return card;
		}

		function renderLensIndexStep() {
			var rule = getAllowedLensIndices(); var recommended = getRecommendedIndex();
			var card = document.createElement('section'); card.className = 'mlr-step is-open'; card.innerHTML = '<header><p>Step 4</p><h3>Select lens thickness</h3></header>';
			var wrap = document.createElement('div'); wrap.className = 'mlr-option-grid';
			Object.keys(schema.lens_indices).forEach(function (key) {
				var option = schema.lens_indices[key]; var allowed = rule.allowed.indexOf(Number(option.id)) >= 0; var disabled = !allowed || rule.disabled.indexOf(Number(option.id)) >= 0;
				var button = document.createElement('button'); button.type = 'button'; button.className = 'mlr-option-card' + (Number(state.lensindex) === Number(option.id) ? ' is-active' : '') + (disabled ? ' is-disabled' : ''); button.disabled = disabled;
				button.innerHTML = '<strong>' + option.label + '</strong><span>' + (recommended === Number(option.id) ? 'Recommended for current Rx.' : 'Available for current flow.') + '</span><em>' + money(getLensIndexPrice(option.id)) + '</em>';
				button.addEventListener('click', function () { state.lensindex = Number(option.id); render(); }); wrap.appendChild(button);
			});
			card.appendChild(wrap); return card;
		}

		function renderCoatingStep() {
			var card = document.createElement('section'); card.className = 'mlr-step is-open'; card.innerHTML = '<header><p>Step 5</p><h3>Select lens coating</h3></header>';
			var wrap = document.createElement('div'); wrap.className = 'mlr-option-grid';
			Object.keys(schema.coatings).forEach(function (key) { var option = schema.coatings[key]; var button = document.createElement('button'); button.type = 'button'; button.className = 'mlr-option-card' + (Number(state.coating) === Number(option.id) ? ' is-active' : ''); button.innerHTML = '<strong>' + option.label + '</strong><span>' + option.description + '</span><em>' + money(getCoatingPrice(option.id)) + '</em>'; button.addEventListener('click', function () { state.coating = Number(option.id); render(); }); wrap.appendChild(button); });
			card.appendChild(wrap);
			var addon = document.createElement('div'); addon.className = 'mlr-addon-toggle' + (bluelightLocked() ? ' is-locked' : '') + (state.bluelight ? ' is-active' : ''); addon.innerHTML = '<div><strong>' + schema.bluelight.label + '</strong><span>' + schema.bluelight.description + '</span></div><em>' + money(schema.bluelight.price[1]) + '</em>';
			addon.addEventListener('click', function () { if (bluelightLocked()) { setStatus('Blue light add-on is locked for this combination.'); return; } state.bluelight = !state.bluelight; render(); });
			card.appendChild(addon); return card;
		}

		function renderPayload() {
			payloadPreview.textContent = state.payload ? JSON.stringify(state.payload, null, 2) : 'Waiting for simulation...';
		}

		function render() {
			ensureValidSelections(); stepsMount.innerHTML = ''; stepsMount.appendChild(renderUsageStep()); stepsMount.appendChild(renderPrescriptionStep()); stepsMount.appendChild(renderLensTypeStep()); stepsMount.appendChild(renderLensIndexStep()); stepsMount.appendChild(renderCoatingStep()); renderSummary(); renderPayload();
		}

		function openDrawer() { overlay.hidden = false; document.body.classList.add('mlr-open-body'); requestAnimationFrame(function () { overlay.classList.add('is-visible'); }); }
		function closeDrawer() { overlay.classList.remove('is-visible'); window.setTimeout(function () { overlay.hidden = true; document.body.classList.remove('mlr-open-body'); }, 240); }

		function maybeHandleProgressiveUpgrade() {
			if (state.usage !== 4 || state.upgradePrompted) { return Promise.resolve(); }
			var currentLensType = getLensTypePrice(state.lenstype);
			var premiumLensTypeTable = schema.pricing.lenstype_price['5'] || schema.pricing.lenstype_price[5];
			var premiumIndexTable = schema.pricing.lensindex_price['5'] || schema.pricing.lensindex_price[5];
			var premiumLensType = premiumLensTypeTable[String(state.lenstype)] || premiumLensTypeTable[state.lenstype];
			var premiumIndex = premiumIndexTable[String(state.lensindex)] || premiumIndexTable[state.lensindex];
			var upgradeDelta = (premiumLensType ? Number(premiumLensType[4]) : currentLensType) - currentLensType + (premiumIndex ? Number(premiumIndex[1]) : getLensIndexPrice(state.lensindex)) - getLensIndexPrice(state.lensindex);
			upgradeCopy.textContent = 'Observed script prompts to upgrade from standard progressive to premium progressive before add-to-cart. Extra lens cost: ' + money(upgradeDelta) + '.';
			upgradeModal.hidden = false;
			return new Promise(function (resolve) {
				function cleanup() { upgradeAccept.removeEventListener('click', accept); upgradeSkip.removeEventListener('click', skip); upgradeModal.hidden = true; }
				function accept() { state.usage = 5; state.upgradePrompted = true; cleanup(); render(); resolve(); }
				function skip() { state.upgradePrompted = true; cleanup(); resolve(); }
				upgradeAccept.addEventListener('click', accept); upgradeSkip.addEventListener('click', skip);
			});
		}

		async function simulateSubmit() {
			if (!validateBeforeSubmit()) { return; }
			await maybeHandleProgressiveUpgrade();
			if (!validateBeforeSubmit()) { return; }
			setStatus('Generating simulated add-to-cart payload...');
			var requestState = { usage: state.usage, lenstype: state.lenstype, lenstype_color: state.lenstype_color, lensindex: state.lensindex, coating: state.coating, pdkey: state.pdkey, nearpd: state.nearpd, prism: state.prism, bluelight: state.bluelight, readers: state.readers, power: state.power, total: getGrandTotal(), form: state.form };
			var body = new window.URLSearchParams(); body.append('action', 'muukal_lens_replica_build_payload'); body.append('nonce', config.nonce); body.append('state', JSON.stringify(requestState));
			var response = await window.fetch(config.ajaxUrl, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' }, body: body.toString(), credentials: 'same-origin' });
			var result = await response.json();
			if (!response.ok || !result.success) { setStatus('Payload generation failed.'); return; }
			state.payload = result.data.payload; renderPayload(); setStatus('Simulated payload generated. No database writes were performed.');
		}

		openButton.addEventListener('click', openDrawer);
		closeButtons.forEach(function (button) { button.addEventListener('click', closeDrawer); });
		copyButton.addEventListener('click', function () { if (!state.payload) { return; } window.navigator.clipboard.writeText(JSON.stringify(state.payload, null, 2)); setStatus('Payload copied to clipboard.'); });
		submitButton.addEventListener('click', function () { simulateSubmit().catch(function () { setStatus('Unexpected error while building payload.'); }); });
		render();
	}

	document.addEventListener('DOMContentLoaded', function () { document.querySelectorAll('.mlr-app').forEach(init); });
})();
