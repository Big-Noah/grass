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
		1: 'https://static.muukal.com/public/static/img/home/frame/lenstype_1.jpg?v=1',
		3: 'https://static.muukal.com/public/static/img/home/frame/lenstype_3.jpg?v=1',
		4: 'https://static.muukal.com/public/static/img/home/frame/lenstype_4.jpg?v=1',
		6: 'https://static.muukal.com/public/static/img/home/frame/lenstype_6.jpg?v=1',
		7: 'https://static.muukal.com/public/static/img/home/frame/lenstype_7.jpg?v=1',
		8: 'https://static.muukal.com/public/static/img/home/frame/lenstype_8.jpg?v=1'
	};
	var lensIndexImages = {
		2: 'https://static.muukal.com/public/static/img/home/frame/thickness_2.png',
		3: 'https://static.muukal.com/public/static/img/home/frame/thickness_3.png',
		4: 'https://static.muukal.com/public/static/img/home/frame/thickness_4.png',
		5: 'https://static.muukal.com/public/static/img/home/frame/thickness_5.png'
	};
	var coatingImages = {
		1: 'https://static.muukal.com/public/static/img/home/frame/coating_1.jpg',
		2: 'https://static.muukal.com/public/static/img/home/frame/coating_2.jpg',
		3: 'https://static.muukal.com/public/static/img/home/frame/coating_3.jpg'
	};
	var replicaCopy = {
		usage: {
			1: {
				heading: 'Single Vision - Distance',
				description: ['General use lenses to see far'],
				priceHtml: 'FREE'
			},
			2: {
				heading: 'Near Vision - Reading',
				description: ['This lens is intended for seeing details at a close distance.', 'Most commonly used for reading.'],
				priceHtml: 'FREE'
			},
			3: {
				heading: 'Bifocal - With Line',
				description: ['To see both near and far away, with a line'],
				priceHtml: '<del>$18.75</del> <span class="mk-price">$15.99</span><span class="lensoff">15% OFF</span>'
			},
			4: {
				heading: 'Progressive - No Line',
				description: ['To see both near and far away, without lines'],
				priceHtml: '$35.99'
			},
			5: {
				heading: 'Premium Progressive - No Line',
				description: ['It has a wider field of vision and can be easier to adapt to compared to the standard progressive.'],
				priceHtml: '<del>$59.99</del> <span class="mk-price">$38.99</span><span class="lensoff">35% OFF</span>'
			},
			20: {
				heading: 'Non-prescription',
				description: ['Lenses without prescription, but you can also choose blue light blocking, tint color and so on'],
				priceHtml: 'FREE'
			}
		},
		lensTypes: {
			1: {
				heading: 'Clear Lenses',
				description: ['Traditional, transparent lenses', 'perfect for everyday use'],
				priceHtml: 'FREE'
			},
			3: {
				heading: 'Photochromic',
				badge: 'Season\'s choice!',
				badgeClass: 'season-choice',
				description: ['Darken when outdoors', 'Remain clear when indoors'],
				priceHtml: '$28.95'
			},
			4: {
				heading: 'Sunglass Tints',
				description: ['Choose from Dark tints in either solid and gradient, turn regular frames into sunglasses'],
				priceHtml: '$10.95'
			},
			6: {
				heading: 'Polarized Sunglasses',
				description: ['Reduce glare and haze for clearer vision'],
				priceHtml: '$31.95'
			},
			7: {
				heading: 'Therapeutic FL-41',
				description: ['A special tint designed for people whose migraines are caused by light sensitivity.'],
				priceHtml: '$49.95'
			},
			8: {
				heading: 'Driving',
				description: ['Classic Clear Driving Lenses, Glare-reducing lenses with water-repellent coating offer sharp, high-definition vision day and night.'],
				priceHtml: '$33.95'
			}
		},
		lensIndices: {
			2: {
				heading: 'Mid-Index 1.55',
				badge: 'Recommended',
				badgeClass: 'recommended-icon',
				description: ['1.55 Index Basic Lenses, ranging from -3.75 to +3.75.'],
				priceHtml: '$5.95'
			},
			3: {
				heading: 'High-Index 1.61',
				badge: 'Recommended',
				badgeClass: 'recommended-icon',
				description: ['1.61 Index Lite & Thin Lenses,up to 25% thinner than the basic 1.55 index lenses'],
				priceHtml: '<del>$23.95</del> <span class="mk-price">$16.65</span><span class="lensoff">30% OFF</span>'
			},
			4: {
				heading: 'Super High-Index 1.67',
				badge: 'Recommended',
				badgeClass: 'recommended-icon',
				description: ['High index 1.67 lenses are thinner and lighter than standard, mid-index, and high-index 1.61 optical lenses.'],
				priceHtml: '$39.95'
			},
			5: {
				heading: 'Ultra High-Index 1.74',
				badge: 'Recommended',
				badgeClass: 'recommended-icon',
				description: ['High index 1.74 polymer lens that provides a thinner lens for stronger prescriptions than mid and standard index optical lenses.'],
				priceHtml: '<del>$82.95</del> <span class="mk-price">$49.95</span><span class="lensoff">40% OFF</span>'
			}
		},
		coatings: {
			1: {
				heading: 'Standard Coatings',
				description: ['All Lenses Come With Free Scratch-resistant Coating'],
				priceHtml: 'FREE'
			},
			2: {
				heading: 'Advanced Coatings',
				description: ['Super Hydrophobic & Anti-Reflective & UV-Protective Coating'],
				priceHtml: '$5.95'
			},
			3: {
				heading: 'Ultimate Coatings',
				description: ['Oleophobic & Hydrophobic & Anti-Reflective & UV-Protective Coating'],
				priceHtml: '<del>$18.95</del> <span class="mk-price">$9.65</span><span class="lensoff">50% OFF</span>'
			}
		}
	};

	function money(value) {
		return '$' + Number(value || 0).toFixed(2);
	}

	function createOption(option, selected) {
		return '<option value="' + option.value + '"' + (String(option.value) === String(selected) ? ' selected' : '') + '>' + option.label + '</option>';
	}

	function buildSelectMarkup(field, options, selected, disabled) {
		return '<div class="lens-select"><select data-field="' + field + '"' + (disabled ? ' disabled' : '') + '>' + options.map(function (option) {
			return createOption(option, selected);
		}).join('') + '</select></div>';
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

	function getReplicaCopy(group, id, fallbackHeading, fallbackDescription) {
		var groupCopy = replicaCopy[group] || {};
		var copy = groupCopy[String(id)] || groupCopy[id] || {};

		return {
			heading: copy.heading || fallbackHeading || '',
			description: copy.description || [fallbackDescription || ''],
			priceHtml: copy.priceHtml || 'FREE',
			badge: copy.badge || '',
			badgeClass: copy.badgeClass || ''
		};
	}

	function renderDescriptionLines(lines) {
		return (lines || []).map(function (line) {
			return '<div class="mlr-card-copy-line">' + line + '</div>';
		}).join('');
	}

	function buildOptionCardMarkup(copy, photoHtml) {
		return '<div class="panel-heading"><span class="mlr-heading-text">' + copy.heading + '</span>' +
			(copy.badge ? '<span class="' + copy.badgeClass + ' mlr-heading-badge">' + copy.badge + '</span>' : '') +
			'<span class="mlr-help-dot" aria-hidden="true">?</span></div>' +
			'<div class="panel-body fs14"><div class="step_div_info row"><div class="col-4">' + photoHtml + '</div><div class="col-8 pdnone"><div class="mlr-card-copy">' + renderDescriptionLines(copy.description) + '</div><div class="mt-10"><span class="lens_k_price">' + copy.priceHtml + '</span></div></div></div></div>';
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
		var closeTimer = null;
		var fixedHost = findFixedContainingBlock(root);

		function findFixedContainingBlock(node) {
			var current = node ? node.parentElement : null;

			while (current && current !== document.body) {
				var styles = window.getComputedStyle(current);
				var containValue = styles.contain || '';

				if (
					styles.transform !== 'none' ||
					styles.perspective !== 'none' ||
					styles.filter !== 'none' ||
					styles.backdropFilter !== 'none' ||
					containValue.indexOf('paint') !== -1
				) {
					return current;
				}

				current = current.parentElement;
			}

			return null;
		}

		function syncOverlayViewport() {
			if (!container) {
				return;
			}

			var hostRect = fixedHost ? fixedHost.getBoundingClientRect() : { left: 0, top: 0 };

			container.style.left = String(-hostRect.left) + 'px';
			container.style.top = String(-hostRect.top) + 'px';
			container.style.right = 'auto';
			container.style.bottom = 'auto';
			container.style.width = String(window.innerWidth) + 'px';
			container.style.height = String(window.innerHeight) + 'px';
		}

		var state = {
			openStep: 1,
			usage: 0,
			readers: 0,
			power: '0',
			lenstype: 0,
			pending_lenstype_color: 0,
			lenstype_color: 0,
			lensindex: 0,
			coating: 0,
			step2Submitted: false,
			pdkey: 1,
			nearpd: 0,
			prism: 0,
			bluelight: false,
			payload: null,
			upgradePrompted: false,
			form: {
				od_sph: '-3.25',
				os_sph: '-4.00',
				od_cyl: '-3.75',
				os_cyl: '-3.75',
				od_axis: '3',
				os_axis: '6',
				od_add: '+2.00',
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
				rx_name: ''
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

		function getLensColorSwatchSrc(colorId) {
			return 'https://static.muukal.com/public/static/img/home/frame/lens_c_' + colorId + '.jpg?v=1';
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
				state.pending_lenstype_color = 0;
				state.lenstype_color = 0;
			}
			var currentLensType = schema.lens_types[String(state.lenstype)] || schema.lens_types[state.lenstype];
			if (!currentLensType || !currentLensType.requires_color) {
				state.pending_lenstype_color = 0;
				state.lenstype_color = 0;
			} else {
				var allowedColors = getLensColorOptions(state.lenstype).map(function (color) {
					return Number(color.id);
				});
				if (allowedColors.indexOf(Number(state.lenstype_color)) === -1) {
					state.lenstype_color = 0;
				}
				if (allowedColors.indexOf(Number(state.pending_lenstype_color)) === -1) {
					state.pending_lenstype_color = Number(state.lenstype_color || 0);
				}
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
			if (!state.usage) {
				setStatus('Choose how you use your glasses first.');
				return false;
			}
			if (state.usage === 20) {
				return true;
			}
			if (!state.step2Submitted) {
				setStatus('Complete the prescription step before adding the lenses to cart.');
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
			state.openStep = Number(step) || 0;
			[1, 2, 3, 4, 5].forEach(function (number) {
				var body = root.querySelector('#step' + number + '_div_box');
				if (body) {
					body.classList.toggle('show', number === state.openStep);
				}
			});
		}

		function toggleOpenStep(step) {
			if (Number(state.openStep) === Number(step)) {
				setOpenStep(0);
				return;
			}

			setOpenStep(step);
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
			} else if (!state.step2Submitted) {
				root.querySelector('#step_2_cn').innerHTML = '';
			} else if (state.usage === 2 && state.readers === 1 && Number(state.power || 0) > 0) {
				root.querySelector('#step_2_cn').innerHTML = 'Readers (+' + state.power + ')&nbsp;&nbsp;<span class="un_line">&nbsp;EDIT&nbsp;</span>';
			} else {
				root.querySelector('#step_2_cn').innerHTML = (state.form.rx_name || 'Prescription') + '&nbsp;&nbsp;<span class="un_line">&nbsp;EDIT&nbsp;</span>';
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
				var copy = getReplicaCopy('usage', option.id, option.label, option.description);
				li.innerHTML = '<div id="step1_li_' + option.id + '" class="panel panel-default lens_key' + (active ? ' lens_k_choose' : '') + '" step="1" val="' + option.id + '">' +
					buildOptionCardMarkup(copy, renderPhoto(usageImages[option.id], option.short_label.charAt(0))) + '</div>';
				li.querySelector('.lens_key').addEventListener('click', function () {
					state.usage = Number(option.id);
					state.readers = 0;
					state.power = '0';
					state.lenstype = 0;
					state.pending_lenstype_color = 0;
					state.lenstype_color = 0;
					state.lensindex = 0;
					state.coating = 0;
					state.step2Submitted = false;
					state.pdkey = 1;
					state.nearpd = 0;
					state.prism = 0;
					state.form.rx_name = '';
					state.upgradePrompted = false;
					state.step2Submitted = Number(option.id) === 20;
					ensureValidSelections();
					setOpenStep(Number(option.id) === 20 ? 3 : 2);
					render();
				});
				mount.appendChild(li);
			});
		}

		function renderPowerBox() {
			var mount = root.querySelector('#power_box');
			mount.style.display = 'none';
			mount.innerHTML = '';
		}

		function renderPrescriptionBox() {
			var mount = root.querySelector('#prescription_box');
			if (state.usage === 20) {
				mount.innerHTML = '<div class="mlr-static-note">Non-prescription selected. This step is skipped and the drawer moves straight to Step 3.</div>';
				return;
			}
			var addDisabled = [1, 20].indexOf(state.usage) >= 0;
			var axisOdDisabled = Number(state.form.od_cyl || 0) === 0;
			var axisOsDisabled = Number(state.form.os_cyl || 0) === 0;
			var pdHtml = state.pdkey === 1
				? '<div class="mlr-pd-selects mlr-pd-selects-single">' + buildSelectMarkup('pd', schema.prescription_fields.pd.options, state.form.pd, false) + '</div>'
				: '<div class="mlr-pd-selects mlr-pd-selects-double">' + buildSelectMarkup('od_pd', schema.prescription_fields.od_pd.options, state.form.od_pd, false) + buildSelectMarkup('os_pd', schema.prescription_fields.os_pd.options, state.form.os_pd, false) + '</div>';
			var prismHtml = state.prism ? '<div class="mlr-prism-grid">' +
				['od_prismnum_v', 'os_prismnum_v', 'od_prismdir_v', 'os_prismdir_v', 'od_prismnum_h', 'os_prismnum_h', 'od_prismdir_h', 'os_prismdir_h'].map(function (field) {
					return '<label>' + field.replace(/_/g, ' ').toUpperCase() + buildSelectMarkup(field, schema.prescription_fields[field].options, state.form[field], false) + '</label>';
				}).join('') + '</div>' : '';
			mount.innerHTML = '<div class="mlr-rx-shell">' +
				'<div class="mlr-rx-toolbar">' +
					'<div class="lens-select mlr-rx-prescription-select"><select aria-label="Select Prescription"><option selected>Select Prescription</option></select></div>' +
					'<button type="button" class="mlr-add-new-link">Add new</button>' +
					'<button type="button" class="mlr-toolbar-help" aria-label="Prescription help">?</button>' +
				'</div>' +
				'<div class="mlr-rx-grid">' +
					'<div class="mlr-rx-grid-head mlr-rx-grid-empty"></div>' +
					'<div class="mlr-rx-grid-head">Sphere(SPH) <span class="mlr-help-inline">?</span></div>' +
					'<div class="mlr-rx-grid-head">Cylinder(CYL) <span class="mlr-help-inline">?</span></div>' +
					'<div class="mlr-rx-grid-head">Axis(AXI) <span class="mlr-help-inline">?</span></div>' +
					'<div class="mlr-rx-grid-head">ADD(Near Addition) <span class="mlr-help-inline">?</span></div>' +
					'<div class="mlr-rx-eye">OD(Right eye)</div>' +
					buildSelectMarkup('od_sph', schema.prescription_fields.od_sph.options, state.form.od_sph, false) +
					buildSelectMarkup('od_cyl', schema.prescription_fields.od_cyl.options, state.form.od_cyl, false) +
					buildSelectMarkup('od_axis', schema.prescription_fields.od_axis.options, state.form.od_axis, axisOdDisabled) +
					buildSelectMarkup('od_add', schema.prescription_fields.od_add.options, state.form.od_add, addDisabled) +
					'<div class="mlr-rx-eye">OS(Left eye)</div>' +
					buildSelectMarkup('os_sph', schema.prescription_fields.os_sph.options, state.form.os_sph, false) +
					buildSelectMarkup('os_cyl', schema.prescription_fields.os_cyl.options, state.form.os_cyl, false) +
					buildSelectMarkup('os_axis', schema.prescription_fields.os_axis.options, state.form.os_axis, axisOsDisabled) +
					buildSelectMarkup('os_add', schema.prescription_fields.os_add.options, state.form.os_add, addDisabled) +
				'</div>' +
				'<div class="mlr-upload-copy">If you are not sure how to enter the prescription, you can upload the picture of the prescription, and we will complete the prescription information for you.</div>' +
				'<div class="mlr-rx-divider"></div>' +
				'<div class="mlr-pd-section">' +
					'<div class="mlr-pd-row">' +
						'<div class="mlr-pd-label">PD(Pupillary Distance) <span class="mlr-help-inline">?</span></div>' +
						pdHtml +
						'<label class="mlr-check"><input type="checkbox" data-toggle="pd"' + (state.pdkey === 2 ? ' checked' : '') + '> Two PD numbers</label>' +
					'</div>' +
					'<div class="mlr-prism-toggle"><label class="mlr-check"><input type="checkbox" data-toggle="prism"' + (state.prism === 1 ? ' checked' : '') + '> Add Prism</label><span class="mlr-prism-price">+$9.95</span></div>' +
					prismHtml +
				'</div>' +
				'<div class="mlr-rx-comments"><label>Comments:</label><textarea data-field="lens_comment">' + (state.form.lens_comment || '') + '</textarea></div>' +
				'<div class="mlr-rx-name"><label>Save prescription As:</label><input type="text" data-field="rx_name" value="' + state.form.rx_name + '" placeholder="prescription name"></div>' +
				'<div class="mlr-upload-row">' +
					'<div class="mlr-upload-copy-block">Not sure of your prescription?<br>Upload your Prescription here:</div>' +
					'<label class="mlr-upload-trigger"><input type="file" aria-label="Upload your prescription photo"><span class="mlr-upload-trigger-inner">UPLOAD</span></label>' +
					'<div class="mlr-upload-note">You can upload your <span>prescription photo</span>, help us check your prescription <span>more accurately</span>.</div>' +
				'</div>' +
				'<div class="text-center mt-20"><button class="btn mlr-step-submit" data-next-step="3">SUBMIT PRESCRIPTION</button></div>' +
			'</div>';

			mount.querySelectorAll('select[data-field], textarea[data-field], input[type="text"][data-field]').forEach(function (input) {
				input.addEventListener('change', function () {
					state.step2Submitted = false;
					state.form[input.getAttribute('data-field')] = input.value;
					ensureValidSelections();
					render();
				});
			});
			mount.querySelectorAll('input[data-toggle]').forEach(function (input) {
				input.addEventListener('change', function () {
					state.step2Submitted = false;
					var type = input.getAttribute('data-toggle');
					if (type === 'pd') {
						state.pdkey = input.checked ? 2 : 1;
					}
					if (type === 'prism') {
						state.prism = input.checked ? 1 : 0;
					}
					render();
				});
			});
			mount.querySelector('.mlr-add-new-link').addEventListener('click', function () {
				state.step2Submitted = false;
				setStatus('');
			});
			mount.querySelector('[data-next-step="3"]').addEventListener('click', function () {
				state.step2Submitted = true;
				setStatus('');
				setOpenStep(3);
				render();
			});
		}

		function buildPairSelect(label, odField, osField, odDisabled, osDisabled) {
			return '<div class="mlr-rx-row"><div class="sr-name-title"><span>' + label + '</span></div>' +
				'<label' + (odDisabled ? ' class="sr-disabled"' : '') + '>' + buildSelectMarkup(odField, schema.prescription_fields[odField].options, state.form[odField], odDisabled) + '</label>' +
				'<label' + (osDisabled ? ' class="sr-disabled"' : '') + '>' + buildSelectMarkup(osField, schema.prescription_fields[osField].options, state.form[osField], osDisabled) + '</label></div>';
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
				var copy = getReplicaCopy('lensTypes', option.id, option.label, option.description);
				var li = document.createElement('li');
				li.className = 'col-xs-6 col-sm-6 col-md-6 col-lg-4';
				li.innerHTML = '<div id="step3_li_' + option.id + '" class="panel panel-default lens_key' + (Number(state.lenstype) === Number(option.id) ? ' lens_k_choose' : '') + (allowed ? '' : ' disable_step_panel') + '" step="3" val="' + option.id + '">' +
					buildOptionCardMarkup(copy, renderPhoto(lensTypeImages[option.id], option.label.charAt(0))) + '</div>';
				var card = li.querySelector('.lens_key');
				if (allowed) {
					card.addEventListener('click', function () {
						var sameLensType = Number(state.lenstype) === Number(option.id);
						state.lenstype = Number(option.id);
						state.pending_lenstype_color = option.requires_color && sameLensType ? Number(state.lenstype_color || 0) : 0;
						state.lenstype_color = option.requires_color && sameLensType ? Number(state.lenstype_color || 0) : 0;
						ensureValidSelections();
						setOpenStep(option.requires_color ? 3 : 4);
						render();
					});
				}
				mount.appendChild(li);
				if (Number(state.lenstype) === Number(option.id) && option.requires_color) {
					var colorLi = document.createElement('li');
					var activeColorId = Number(state.pending_lenstype_color || state.lenstype_color || 0);
					var activeColor = schema.lens_colors[String(activeColorId)] || schema.lens_colors[activeColorId];
					colorLi.className = 'col-12 mb-20 lens_entry_info';
					colorLi.id = 'lenstype_sec_' + option.id;
					var colors = getLensColorOptions(option.id).map(function (color) {
						return '<span class="mid-cspan color_btn' + (activeColorId === Number(color.id) ? ' mid-choose-color' : '') + '" data-color="' + color.id + '" data-color-name="' + color.label + '">' +
							'<img class="lens_c_icon" src="' + getLensColorSwatchSrc(color.id) + '" alt="' + color.label + '">' +
						'</span>';
					}).join('');
					colorLi.innerHTML = '<div class="borderd7 mlr-color-panel">' +
						'<div class="mlr-color-panel-heading">&nbsp;&nbsp;TINT COLOR:&nbsp;&nbsp;<span class="dark-s">Darkness 80%</span></div>' +
						'<div class="mid-color-box">' + colors + '</div>' +
						'<button type="button" class="btn theme-btn-s lens_key color-confirm-btn">' +
							'CONFIRM' + (activeColor ? '&nbsp;&nbsp;(' + activeColor.label + ')' : '') +
						'</button>' +
					'</div>';
					mount.appendChild(colorLi);
					colorLi.querySelectorAll('[data-color]').forEach(function (button) {
						button.addEventListener('click', function () {
							state.pending_lenstype_color = Number(button.getAttribute('data-color'));
							setStatus('');
							render();
						});
					});
					colorLi.querySelector('.color-confirm-btn').addEventListener('click', function () {
						if (!state.pending_lenstype_color) {
							setStatus('Pick a tint color first.');
							return;
						}
						state.lenstype_color = Number(state.pending_lenstype_color);
						ensureValidSelections();
						setStatus('');
						setOpenStep(4);
						render();
					});
					colorLi.querySelectorAll('.lens_c_icon').forEach(function (image) {
						image.addEventListener('error', function () {
							image.setAttribute('alt', image.getAttribute('alt') || 'Lens color');
							image.style.background = '#f5f5f5';
							image.style.opacity = '0.35';
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
				var copy = getReplicaCopy('lensIndices', option.id, option.label, 'Available for the current prescription flow.');
				var li = document.createElement('li');
				li.className = 'col-6 col-lg-6';
				li.innerHTML = '<div id="step4_li_' + option.id + '" class="panel panel-default step4_panel lens_key' + (Number(state.lensindex) === Number(option.id) ? ' lens_k_choose' : '') + (disabled ? ' disable_step_panel' : '') + '" step="4" val="' + option.id + '">' +
					buildOptionCardMarkup({
						heading: copy.heading,
						description: copy.description,
						priceHtml: copy.priceHtml,
						badge: recommended === Number(option.id) ? 'Recommended' : copy.badge,
						badgeClass: 'recommended-icon'
					}, renderPhoto(lensIndexImages[option.id], option.label.charAt(0))) + '</div>';
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
				var copy = getReplicaCopy('coatings', option.id, option.label, option.description);
				var li = document.createElement('li');
				li.className = 'col-xs-6 col-sm-6 col-md-6 col-lg-4';
				li.innerHTML = '<div id="step5_li_' + option.id + '" class="panel panel-default lens_key' + (Number(state.coating) === Number(option.id) ? ' lens_k_choose' : '') + '" step="5" val="' + option.id + '">' +
					buildOptionCardMarkup(copy, renderPhoto(coatingImages[option.id], option.label.charAt(0))) + '</div>';
				li.querySelector('.lens_key').addEventListener('click', function () {
					state.coating = Number(option.id);
					setStatus('');
					render();
					showSummaryView();
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
			root.querySelector('#data_attr_1').style.display = usage ? 'block' : 'none';
			root.querySelector('#data_attr_2').style.display = state.prism ? 'block' : 'none';
			root.querySelector('#data_attr_3').style.display = lensType ? 'block' : 'none';
			root.querySelector('#data_attr_4').style.display = lensIndex ? 'block' : 'none';
			root.querySelector('#data_attr_5').style.display = coating ? 'block' : 'none';
			root.querySelector('#data_attr_6').style.display = usage ? 'block' : 'none';
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

		function showSummaryView() {
			var left = root.querySelector('#lensbox_left');
			var right = root.querySelector('#lensbox_right');
			left.style.display = 'none';
			right.classList.remove('bounceInRight', 'mlr-summary-enter');
			right.classList.add('col-full');
			void right.offsetWidth;
			right.classList.add('bounceInRight', 'mlr-summary-enter');
			editAgain.style.display = 'block';
		}

		function hideSummaryView() {
			var left = root.querySelector('#lensbox_left');
			var right = root.querySelector('#lensbox_right');
			left.style.display = '';
			right.classList.remove('col-full', 'bounceInRight', 'mlr-summary-enter');
			editAgain.style.display = 'none';
		}

		function openOverlay() {
			if (closeTimer) {
				window.clearTimeout(closeTimer);
				closeTimer = null;
			}
			syncOverlayViewport();
			container.hidden = false;
			document.body.classList.add('mlr-open-body');
			render();
			window.requestAnimationFrame(function () {
				syncOverlayViewport();
				container.classList.add('is-open');
				root.querySelector('#lens_box').scrollTop = 0;
			});
		}

		function closeOverlay() {
			container.classList.remove('is-open');
			document.body.classList.remove('mlr-open-body');
			hideSummaryView();
			closeTimer = window.setTimeout(function () {
				container.hidden = true;
				closeTimer = null;
			}, 380);
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
			showSummaryView();
			setStatus('Lens payload generated.');
		}

		openButton.addEventListener('click', openOverlay);
		closeButtons.forEach(function (button) {
			button.addEventListener('click', closeOverlay);
		});
		root.querySelectorAll('.mlr-step-toggle').forEach(function (toggle) {
			toggle.addEventListener('click', function () {
				toggleOpenStep(Number(toggle.getAttribute('data-step')));
				render();
			});
		});
		window.addEventListener('resize', function () {
			if (!container.hidden || container.classList.contains('is-open')) {
				syncOverlayViewport();
			}
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
			hideSummaryView();
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
