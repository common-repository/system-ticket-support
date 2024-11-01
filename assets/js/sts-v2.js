var STS = STS || {};
(function ($) {
	'use strict';

	/**
	 * Global variable
	 *
	 * @type {*|HTMLElement}
	 */
	var $body = $('body'),
		$document = $(document),
		$window = $(window);

	STS = {
		_ajaxRequesting: false,

		init: function () {
			this.settingTinyMCE();
			this.attachmentGallery();
			this.addCarousel();
			this.processChooseProductFormSubmitTicket();
			this.openFormRating();
			this.processFormAction();
			this.processLinkAction();
			this.processChangeAction();
			this.select2Replace();
			this.paginatorAction();
			this.formNewNoteProcess();
			this.cancelEditMessage();
			this.leftSidebarProcess();
			this.formSubmitTicketValidate();
			this.formRegisterValidate();
			this.dropdownMenuLeft();
			this.dashboardsClock();
			this.dashboardChart();
			this.processFormUpdateTicketMeta();
			this.listingticketSendRequest();
			this.datePicker();
			this.popupProcess();
			this.formUpdateProfileValidate();
			this.ticketDetailsSendRequest();
			this.reportChart();
			this.sticky();
			this.processScrollAction();
			this.changeCurrentPage();
			this.closeAnchor();
			this.getHastag();
		},
		getHastag: function () {
			var $redirectEl = $('#sts-redirect');
			if ($redirectEl.length < 1) {
				return;
			}
			var hash = location.hash;
			if (hash !== undefined) {
				var url = $redirectEl.val();
				$redirectEl.val(url + hash);
			}
		},
		closeAnchor: function () {
			$document.on('click', '.supporter-processing-anchor-close', function (e) {
				e.preventDefault();
				var $containerCLose = $('.supporter-processing-anchor');
				if (!$containerCLose.hasClass('hide')) {
					$containerCLose.addClass('hide')
				}
			})
		},
		addCarousel: function () {
			var $carouselEl = $('#custom_carousel');
			$carouselEl.carousel({
				interval: false,
			});
			$carouselEl.on('slide.bs.carousel', function (ev) {
				var $index = $(ev.relatedTarget).index();
				var $tabEl = $('[data-slide-to=' + $index + ']').addClass('ripple');
			});
			$carouselEl.on('slid.bs.carousel', function () {
				var $tabEls = $(this).find('.tab-item');
				$.each($tabEls, function () {
					$(this).removeClass('ripple');
				})
			})
		},

		changeCurrentPage: function () {
			var $current_page = $('#current-page');
			if ($current_page.length < 1) {
				return;
			}
			$('#search').on('change', function (e) {
				e.preventDefault();
				if ($current_page.val() !== 1) {
					$current_page.val(1);
				}
			});
			$('#ftstatus').on('change', function (e) {
				e.preventDefault();
				if ($current_page.val() !== 1) {
					$current_page.val(1);
				}
			});
			$('#theme').on('change', function (e) {
				e.preventDefault();
				if ($current_page.val() !== 1) {
					$current_page.val(1);
				}
			});
			$('#from-date').on('change', function (e) {
				e.preventDefault();
				if ($current_page.val() !== 1) {
					$current_page.val(1);
				}
			});
			$('#to-date').on('change', function (e) {
				e.preventDefault();
				if ($current_page.val() !== 1) {
					$current_page.val(1);
				}
			});

		},

		sticky: function () {
			$('#purchase-code-details').hcSticky({top: 10});
		},

		settingTinyMCE: function () {
			var options = {
				selector: '.sts-text-editor',
				height: 300,
				paste_as_text: true,
				menubar: false,
				branding: false,
				//toolbar_drawer: 'sliding',
				plugins: 'codesample,paste,link,lists,fullscreen',
				codesample_languages: [
					{text: 'HTML/XML', value: 'markup'},
					{text: 'JavaScript', value: 'javascript'},
					{text: 'CSS', value: 'css'},
					{text: 'PHP', value: 'php'}
				],
				style_formats: [
					{title: 'Paragraph', block: 'p'},
					{title: 'Heading 3', block: 'h3'},
					{title: 'Heading 4', block: 'h4'},
					{title: 'Heading 5', block: 'h5'},
					{title: 'Heading 6', block: 'h6'},
					{title: 'Preformatted', block: 'pre'},
					{title: 'Block Quote', block: 'blockquote'},
					{title: 'Strikethrough', block: 'strike'}
				],
				toolbar: 'undo redo | styleselect | bold italic underline | subscript superscript | forecolor backcolor | alignleft aligncenter alignright | bullist numlist outdent indent | removeformat | link codesample',
				content_style: "body {font-family: 'Muli', sans-serif;line-height: 24px;} "
			};

			if (STS_META_DATA.is_moderator) {
				options.plugins += ',code';
				options.toolbar += '| code';
			}
			options.toolbar += ' | fullscreen';

			tinymce.init(options);
		},

		reportChartInit: function (ctx, labels, data, bgcolor, bdcolor, legend) {
			var myChart = new Chart(ctx, {
				type: 'doughnut',
				data: {
					labels: labels,
					datasets: [{
						label: 'Ticket',
						data: data,
						backgroundColor: bgcolor,
						borderColor: bdcolor,
						borderWidth: 1
					}]
				},
				options: {
					legend: {
						display: false
					},

					legendCallback: function (chart) {
						// Return the HTML string here.
						var text = [];
						text.push('<ul class="' + chart.id + '-legend">');
						for (var i = 0; i < chart.data.datasets[0].data.length; i++) {
							text.push('<li>');
							text.push('<span style="background-color:' + chart.data.datasets[0].backgroundColor[i] + '">' + '</span>');
							if (chart.data.labels[i]) {
								text.push(chart.data.labels[i]);
							}
							text.push('</span></li>');
						}
						text.push('</ul>');
						return text.join("");
					},
					cutoutPercentage: 70,
					scales: {
						yAxes: [{
							ticks: {
								beginAtZero: true,
								callback: function (value, index, values) {
									return '';
								}
							},
							gridLines: {
								display: false,
								drawBorder: false
							}
						}],
						xAxes: [{
							ticks: {
								beginAtZero: true,
								callback: function (value, index, values) {
									return '';
								}
							},
							gridLines: {
								display: false,
								drawBorder: false
							}
						}]
					}
				}
			});
			return myChart;
		},

		attachmentGallery: function () {
			var $galleryEl = $('.single-ticket__attachment');
			if ($galleryEl.length < 1) {
				return;
			}
			$galleryEl.magnificPopup({
				delegate: 'a',
				type: 'image',
				closeOnContentClick: false,
				closeBtnInside: false,
				mainClass: 'mfp-with-zoom mfp-img-mobile',
				image: {
					verticalFit: true,
					titleSrc: function (item) {
						return item.el.attr('title');
					}
				},
				gallery: {
					enabled: true
				},
				zoom: {
					enabled: false,
				}
			});
		},

		//Choose product submit ticket
		processChooseProductFormSubmitTicket: function () {
			var $formContainerEl = $('.form-container');
			if ($formContainerEl.length < 1) {
				return;
			}
			var $productEl = $('#product');
			$productEl.on('change', function (e) {
				e.preventDefault();
				if ($(this).val() === '') {
					$formContainerEl.addClass('close');
				} else {
					$formContainerEl.removeClass('close');
				}
			});
		},

		//Open form rating
		openFormRating: function () {
			var hash = window.location.hash;
			if (hash === "#form-rating") {
				$('#form-rating').removeClass('close');
			} else {
				var $moreMessageEl = $('.single-ticket__more-message');
				var $message = $moreMessageEl.find(hash);
				if ($message.length > 0) {
					$('#sts-more-message').collapse('show');
				}
			}
		},

		//Open manific popup
		popupProcess: function () {
			$('.popup-with-form').magnificPopup({
				type: 'inline',
				preloader: false,
				callbacks: {}
			});
		},

		//datepicker
		datePicker: function () {
			$(".sts-datepicker").datepicker({
				changeMonth: true,
				changeYear: true,
				dateFormat: "yy-mm-dd"
			});
		},

		//Replace all select tag by select2
		select2Replace: function () {
			$('select.form-control').select2();
			$('.sts-template-tags').select2({
				tags: true,
				tokenSeparators: [','],
				multiple: true,
				allowClear: true,
			});
			$('#sts-rely-template').select2({
				matcher: function (params, data) {
					if ($.trim(params.term) === '') {
						return data;
					}
					if (typeof data.element.dataset.search === 'undefined') {
						return null;
					}
					if (data.element.dataset.search.indexOf(params.term) > -1) {
						return $.extend({}, data, true);
					}
					return null;
				}
			});
		},

		//Paginator processing
		paginatorAction: function () {
			var $currentPageEls = $('.current-page');
			$.each($currentPageEls, function () {
				$(this).val(1);
			});
			$(document).on('click', '.sts-paginator__item', function (e) {
				e.preventDefault();
				$(this).addClass('disable');
				var $pagnatorEl = $(this).parent('.sts-paginator');
				var $formEl = $($pagnatorEl.data('sts-target'));
				var $currentPageEl = $formEl.find('.current-page');
				var currentpage;
				if ($(this).hasClass('sts-paginator__item-numb')) {
					currentpage = $(this).data('page');
				} else if ($(this).hasClass('sts-paginator__item--prev')) {
					currentpage = $currentPageEl.val();
					currentpage = parseInt(currentpage) - 1;
				} else if ($(this).hasClass('sts-paginator__item--next')) {
					currentpage = $currentPageEl.val();
					currentpage = parseInt(currentpage) + 1;
				}
				$currentPageEl.val(currentpage);
				$formEl.submit();
				return false;
			});
		},

		//Show and close form new note
		formNewNoteProcess: function () {
			var $noteNewEl = $(".note__new-form");
			if ($noteNewEl.length < 1) {
				return;
			}
			var $buttonNewEl = $(".new-note");
			var $buttonCancelEl = $(".button-cancel");
			if ($noteNewEl.length < 1) {
				return;
			}

			$buttonNewEl.on("click", function (e) {
				e.preventDefault();
				if ($noteNewEl.hasClass("close")) {
					$noteNewEl.removeClass("close");
				}
			});
			$buttonCancelEl.on("click", function (e) {
				e.preventDefault();
				if (!$noteNewEl.hasClass("close")) {
					$noteNewEl.addClass("close");
				}
			});
		},

		cancelEditMessage: function () {
			$document.on('click', '.button-cancel-edit', function (event) {
				var $parent = $(this).parents('.single-ticket__container');
				var $formEL = $parent.find('.message-action__form');
				var $buttonEl = $parent.find('.message-action__item--edit');
				if (!$formEL.hasClass('close')) {
					$formEL.addClass('close');
					$buttonEl.removeClass('close');
				}
			});
		},

		//Processing for left nav
		leftSidebarProcess: function () {
			var $pageContentEl = $(".sts-page-content");
			var $pageWrapper = $('.sts-page-wrapper');
			var $leftSidebarEl = $(".left-sidebar");
			var $pageToggleEl = $(".page-toggle");
			var $subMenu = $('.multi-menu__sub');
			var $pageClose = $('.page-close');
			var isMouseout = false;
			var countClick = 1;
			if ($pageContentEl.hasClass("hide")) {
				$pageContentEl.removeClass("hide");
			}
			if ($leftSidebarEl.length < 1) {
				return;
			}

			$pageToggleEl.on("click", function (e) {
				e.preventDefault();
				if (countClick === 0) {
					$(this).addClass('active');
					$pageContentEl.removeClass("hide");
					$subMenu.removeClass('close');
					$pageWrapper.removeClass('hide');
					isMouseout = false;
				} else {
					$pageContentEl.addClass("hide");
					$subMenu.addClass('close');
					$pageWrapper.addClass('hide');
					isMouseout = true;
				}
				countClick = 1;
				return false;
			});
			$pageToggleEl.on('focus', function (e) {
				$(this).addClass('active');
			});
			$pageToggleEl.on('blur', function (e) {
				$(this).removeClass('active');
			});
			$leftSidebarEl.on("mouseout", function (e) {
				e.preventDefault();
				$pageToggleEl.blur();
				if (isMouseout === true) {
					$pageContentEl.addClass("hide");
					$subMenu.addClass('close');
					countClick = 0;
				}
				return false;
			});
			$leftSidebarEl.on("mouseover", function (e) {
				e.preventDefault();
				$pageToggleEl.blur();
				$pageContentEl.removeClass("hide");
				$subMenu.removeClass('close');
				return false;
			});
			$(".top-bar__toggle").on("click", function (e) {
				e.preventDefault();
				$leftSidebarEl.addClass("show-left-nav");
				return false;
			});
			$pageClose.on("click", function (e) {
				e.preventDefault();
				$leftSidebarEl.removeClass("show-left-nav");
				return false;
			});
		},

		//validate form submit ticket
		formSubmitTicketValidate: function () {
			var $formEl = $(".form-submit-ticket");
			if ($formEl.length < 1) {
				return;
			}
			$('#product').val('');
			$formEl.validate({
				rules: {
					"theme": {
						required: true,
					},
					"firstName": {
						required: true,
					},
					"lastName": {
						required: true,
					},
					"email": {
						required: true,
						email: true
					},
					"password": {
						required: true,
					},
					"rppassword": {
						equalTo: "#password"
					},
					"purchaseCode": {
						required: true,
					},
					"ticketSubject": {
						required: true,
					},
					"relatedUrl": {
						required: true,
						url: true
					},
				}
			});

		},

		//validate form register
		formRegisterValidate: function () {
			var $formEl = $(".form-register");
			if ($formEl.length < 1) {
				return;
			}
			$formEl.validate({
				rules: {
					"firstName": {
						required: true,
					},
					"lastName": {
						required: true,
					},
					"email": {
						required: true,
						email: true
					},
					"password": {
						required: true,
					},
					"rppassword": {
						equalTo: "#password"
					},
					"purchaseCode": {
						required: true,
					},
				}
			});
		},

		//validate form register
		formUpdateProfileValidate: function () {
			var $formEl = $(".form-update-profile");
			if ($formEl.length < 1) {
				return;
			}
			$formEl.validate({
				rules: {
					"firstName": {
						required: true,
					},
					"lastName": {
						required: true,
					},
					"rppassword": {
						equalTo: "#password"
					},
				}
			});
		},

		//Dropdown menu for left nav
		dropdownMenuLeft: function () {
			var $multiEls = $('.multi-menu');
			if ($multiEls.length < 1) {
				return;
			}
			$.each($multiEls, function () {
				var $toggleEl = $(this).find('.multi-menu__toggle');
				var $iconRignhtEl = $(this).find('.arrow-right');
				$toggleEl.on('click', function (e) {
					e.preventDefault();
					if ($iconRignhtEl.hasClass('open')) {
						$iconRignhtEl.removeClass('open');

					} else {
						$iconRignhtEl.addClass('open');
					}
				});
			});
		},

		//set clock for dashboards
		dashboardsClock: function () {
			var $dOut = $('.dashboard__time-clock-day'),
				$hOut = $('.dashboard__time-clock-hours'),
				$mOut = $('.dashboard__time-clock-minute'),
				$sOut = $('.dashboard__time-clock-second');
			if ($dOut.length < 1 || $hOut.length < 1 || $mOut.length < 1 || $sOut.length < 1) {
				return;
			}
			var months = [
				'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
			];

			var days = [
				'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
			];

			window.setInterval(function () {
				var date = new Date();

				var hours = date.getHours();

				var minutes = date.getMinutes() < 10
					? '0' + date.getMinutes()
					: date.getMinutes();

				var seconds = date.getSeconds() < 10
					? '0' + date.getSeconds()
					: date.getSeconds();

				var dayOfWeek = days[date.getDay()];
				var month = months[date.getMonth()];
				var day = date.getDate();
				var year = date.getFullYear();

				$dOut.text(dayOfWeek + ', ');
				$hOut.text(hours + ' :');
				$mOut.text(minutes + ' :');
				$sOut.text(seconds);
				$('.dashboard__time-month').text(month);
				$('.dashboard__time-day').text(day);
				$('.dashboard__time-year').text(year);
			}, 5)
		},

		//Dashboards chart
		dashboardChart: function () {
			var $container = $(".dashboard__chart");
			if ($container.length < 1) {
				return;
			}
			var $allVal = parseInt($('.dashboard__report-number--all').text(), 10);
			var $requestVal = parseInt($('.dashboard__report-number--request').text(), 10);
			var $responded = parseInt($('.dashboard__report-number--responded').text(), 10);
			var $closeVal = parseInt($('.dashboard__report-number--close').text(), 10);
			var $otherVal = $allVal - ($requestVal + $responded + $closeVal);
			var $ctx = $('#myChart');
			var myChart = STS.reportChartInit($ctx, ['Ticket request', 'Ticket responded', 'Ticket closed', 'Other ticket'],
				[$requestVal, $responded, $closeVal, $otherVal],
				[
					'#bf0000',
					'#f44336',
					'#09d261',
					'#039be5'
				], [
					'#bf0000',
					'#f44336',
					'#09d261',
					'#039be5'
				], true);
			$(".chart-legend").html(myChart.generateLegend());
		},

		//Report chart
		reportChart: function () {
			var $container = $(".report__chart");
			if ($container.length < 1) {
				return;
			}
			var $satisfied = parseInt($('#sts-satisfied').text(), 10);
			var $unsatisfied = parseInt($('#sts-unsatisfied').text(), 10);
			var $visited = parseInt($('#sts-visited').text(), 10);
			var $other = parseInt($('#sts-other').text(), 10);
			var $ctx = $('#reportChart');
			STS.reportChartInit($ctx, ['Unsatisfied', 'Satisfied', 'Visited not rating', 'Other'],
				[$unsatisfied, $satisfied, $visited, $other],
				[
					'rgba(255, 99, 132, 0.2)',
					'rgba(54, 162, 235, 0.2)',
					'#bf0000',
					'#09d261'
				], [
					'rgba(255, 99, 132, 1)',
					'rgba(54, 162, 235, 1)',
					'#bf0000',
					'#09d261'
				], false);
		},
		//Show form update status and supporter
		processFormUpdateTicketMeta: function () {
			var $formWrapperEls = $('.ticket-metadata__can-update');
			if ($formWrapperEls.length < 1) {
				return
			}
			$.each($formWrapperEls, function () {
				var $linkClick = $(this).find('.ticket-metadata__action');
				var $formEl = $(this).find('.sts-form-close');
				$linkClick.on('click', function (e) {
					e.preventDefault();
					if ($formEl.hasClass('close')) {
						$formEl.removeClass('close')
					}
				});
				var $buttonCacel = $(this).find('.button-cancel-update');
				$buttonCacel.on('click', function (e) {
					e.preventDefault();
					if (!$formEl.hasClass('close')) {
						$formEl.addClass('close')
					}
				})
			})

		},

		listingticketSendRequest: function () {
			var $ticketInitEl = $('.listing-ticket__initing');
			var ticketIDs = $ticketInitEl.attr('data-ticket-id');
			if ($ticketInitEl.length < 1 || ticketIDs === undefined) {
				return;
			}
			var nonce = $ticketInitEl.data('nonce');
			var param = {};
			param.ticket_ids = ticketIDs;
			param.nonce = nonce;
			param.action = 'sts_auto_sending';
			setInterval(function () {
				$.when($.ajax({
					type: "post",
					url: ajaxAdminUrl.url,
					data: param
				})).then(function (res) {
					if (res.data !== '') {
						var arr = res.data;
						$.each(arr, function (index) {
							var $processEl = $(arr[index]['target']);
							if ($processEl.length > 0) {
								$processEl.html(arr[index]['supporter']);
							}
						})
					}
				})
			}, 3000);
		},

		ticketDetailsSendRequest: function () {
			var $processEl = $('.single-ticket__meta-process');
			if ($processEl.length < 1) {
				return;
			}
			var nonce = $processEl.data('nonce');
			var ticket_id = $processEl.data('ticket');
			var param = {};
			param.ticket_id = ticket_id;
			param.nonce = nonce;
			param.action = 'sts_ticket_details_auto_sending';
			setInterval(function () {
				$.when($.ajax({
					type: "post",
					url: ajaxAdminUrl.url,
					data: param
				})).then(function (res) {
					var arr = res.data;
					if (arr !== '') {
						$(arr.target).html(arr.content);
						$(arr.target_unmark).html(arr.content_unmark);
						if (arr.open_content_anchor !== undefined) {
							var $anchor = $(arr.open_content_anchor);
							if (!$anchor.hasClass('hide') && $anchor.hasClass('close')) {
								$anchor.removeClass('close');
							}
						}
					}
					STS.showClosedContent(res);
				})
			}, 3000);
		},

		processScrollAction: function () {
			var $currentPageEl = $('#notification-current-page');
			$currentPageEl.val(2);
			$('[data-sts-scroll-action]').scroll(function (event) {
				event.preventDefault();
				var $this = $(this);
				var height = this.scrollHeight - $this.height();
				var scroll = $this.scrollTop();
				var isScrolledToEnd = (scroll >= height);
				var current_page = parseInt($currentPageEl.val());
				var total_page = parseInt($this.find('#total-page').val());

				var self = this,
					action = $this.data('sts-scroll-action'),
					param = $this.data('sts-action-param'),
					callback = $this.data('sts-callback'),
					errorCallback = $this.data('sts-error-callback'),
					confirmMessage = $this.data('sts-confirm');
				param.current_page = current_page;

				if (isScrolledToEnd && current_page <= total_page) {
					if (confirmMessage) {
						STS.confirm(confirmMessage, function () {
							STS.ajaxRequest.call(self, action, param, callback, errorCallback);
						});
					} else {
						STS.ajaxRequest.call(self, action, param, callback, errorCallback);
					}
				}
				return false;
			});
		},

		processFormAction: function () {
			$document.on('submit', '[data-sts-form-action]', function (event) {
				event.preventDefault();
				$('.form-control-message,.form__message').html('');
				var $editor = $('.sts-editor');
				if ($editor.length > 0) {
					tinyMCE.triggerSave();
				}

				var self = this,
					$this = $(this),
					callback = $this.data('sts-callback'),
					errorCallback = $this.data('sts-error-callback'),
					confirmMessage = $this.data('sts-confirm'),
					action = $this.data('sts-form-action');

				var formData = new FormData(self);

				if (confirmMessage) {
					STS.confirm(confirmMessage, function () {
						STS.ajaxRequest.call(self, action, formData, callback, errorCallback);
					});
				} else {
					STS.ajaxRequest.call(self, action, formData, callback, errorCallback);
				}
				return false;
			});
		},

		processLinkAction: function () {
			$document.on('click', '[data-sts-action]', function (event) {
				event.preventDefault();
				$('.form__message').html('');
				var self = this,
					$this = $(this),
					action = $this.data('sts-action'),
					param = $this.data('sts-action-param'),
					callback = $this.data('sts-callback'),
					errorCallback = $this.data('sts-error-callback'),
					confirmMessage = $this.data('sts-confirm');

				if (confirmMessage) {
					STS.confirm(confirmMessage, function () {
						STS.ajaxRequest.call(self, action, param, callback, errorCallback);
					});
				} else {
					STS.ajaxRequest.call(self, action, param, callback, errorCallback);
				}
				return false;
			});
		},

		processChangeAction: function () {
			$document.on('change', '[data-sts-change-action]', function (event) {
				event.preventDefault();
				$('.form__message').html('');
				var self = this,
					$this = $(this),
					action = $this.data('sts-change-action'),
					param = $this.data('sts-action-param'),
					callback = $this.data('sts-callback'),
					errorCallback = $this.data('sts-error-callback'),
					confirmMessage = $this.data('sts-confirm');
				param.id = $(this).val();
				if (param.id !== '') {
					if (confirmMessage) {
						STS.confirm(confirmMessage, function () {
							STS.ajaxRequest.call(self, action, param, callback, errorCallback);
						});
					} else {
						STS.ajaxRequest.call(self, action, param, callback, errorCallback);
					}
				} else {
					var $content_close = $(param.close_content);
					if (!$content_close.hasClass('close')) {
						$content_close.addClass('close');
					}
				}
				return false;
			});
		},

		ajaxRequest: function (action, param, callback, error_callback) {
			/**
			 * Prevent call ajax when ajax requesting
			 */
			if (STS._ajaxRequesting) {
				return;
			}

			/**
			 * Mark ajax requesting
			 */
			STS._ajaxRequesting = true;
			if (!param) {
				param = {};
			}

			if (this.tagName === 'FORM') {
				param.append('action', action);
			} else {
				param.action = action;
			}

			if (callback && (typeof (callback) === 'string')) {
				callback = STS.getFunctionByName(callback);
			}
			if (error_callback && (typeof (error_callback) === 'string')) {
				error_callback = STS.getFunctionByName(error_callback);
			}

			var self = this;
			var ajaxOptions = {
				type: 'POST',
				url: ajaxAdminUrl.url,
				data: param,
				success: function (res) {
					switch (res.status) {
						case 'delete':
							$(res.data.target).fadeOut(function () {
								$(this).remove();
							});
							break;
						case 'append':
							$(res.data.target).append(res.data.content);
							break;

						case 'prepend':
							$(res.data.target).prepend(res.data.content);
							break;

						case 'update':
							$(res.data.target).html(res.data.content);
							if (res.data.target_paginator !== undefined) {
								$(res.data.target_paginator).html(res.data.content_paginator);
							}
							break;

						case 'replace':
							$(res.data.target).empty();
							$(res.data.target).html(res.data.content);
							break;

						case 'alert':
							STS.alert(res);
							break;

						case 'alert-success':
							STS.alertSuccess(res);
							break;

						case 'redirect':
							window.location.href = res.data;
							break;
						case 'alert-multi':
							if (res.data.error_arr.length > 0) {
								$.each(res.data.error_arr, function (key, value) {
									$(value.target).html('<div class="form__message--error">' + value.message + '</div>');
								});
								$('html, body').animate({
									scrollTop: ($(res.data.error_arr[0].target).parent().offset().top)
								}, 500);
								break;
							}
					}
					if (callback) {
						callback.call(self, res);
					}
				},
				error: function (res) {
					if (error_callback) {
						error_callback(self, res);
					}
				},
				complete: function () {
					/**
					 * Allow call ajax when ajax request done
					 */
					STS._ajaxRequesting = false;
					if (self._ladda) {
						self._ladda.stop();
					}
					if (self.$_loading) {
						self.$_loading.stop().fadeOut(function () {
							$(this).remove();
						});
						self.$_loading = null;
					}
				}
			};

			if (this.tagName === 'FORM') {
				ajaxOptions.processData = false;
				ajaxOptions.contentType = false;
			}
			if (this.tagName === 'FORM') {
				if ($(this).data('sts-ladda')) {
					var $button = $(this).find("[type=submit]")[0];
					if (!$($button).hasClass('ladda-button')) {
						$($button).addClass('ladda-button');
					}
					if (!$($button).attr('data-style')) {
						$($button).attr('data-style', 'slide-up');
					}
					if (!$($button).attr('data-spinner-color')) {
						$($button).attr('data-spinner-color', '#fff');
					}
					if (!$($button).attr('data-size')) {
						$($button).attr('data-size', 'l');
					}

					self._ladda = Ladda.create($button);
					self._ladda.start();
				}
			} else {
				if ($(this).data('sts-ladda')) {
					if (!$(this).hasClass('ladda-button')) {
						$(this).addClass('ladda-button');
					}
					if (!$(this).attr('data-style')) {
						$(this).attr('data-style', 'zoom-out');
					}
					if (!$(this).attr('data-spinner-color')) {
						$(this).attr('data-spinner-color', '#000000');
					}

					self._ladda = Ladda.create(this);
					self._ladda.start();
				}

			}

			STS.showLoading.call(this);

			$.ajax(ajaxOptions);
		},

		/**
		 * Show loading
		 *
		 * @param selector
		 */
		showLoading: function (selector) {
			var $loadingWrap;

			if (selector) {
				$loadingWrap = $(selector);
			} else {
				var $this = $(this),
					loadingSelector = $this.data('sts-loading'),
					loadingClosest = $this.data('sts-loading-closest');
				if (loadingSelector !== undefined) {
					if (loadingSelector !== '') {
						$loadingWrap = $(loadingSelector);
					} else {
						$loadingWrap = $body;
					}
				} else if (loadingClosest !== undefined) {
					if (loadingClosest !== '') {
						$loadingWrap = $(this).closest(loadingClosest);

					} else {
						if ($(this).closest('.sts-popup-content').length > 0) {
							$loadingWrap = $(this).closest('.sts-popup-content');
						} else {
							$loadingWrap = $body;
						}
					}
				}
			}

			if ($loadingWrap && ($loadingWrap.length > 0)) {
				if ($loadingWrap.css('position') === 'static') {
					$loadingWrap.css('position', 'relative');
				}
				this.$_loading = $('<div class="sts-loading" style="display: none"><span></span></div>');
				$loadingWrap.append(this.$_loading);
				this.$_loading.fadeIn();
			}
		},

		alert: function (res) {
			var $messageEl = $(res.data.target);
			if ($messageEl !== undefined) {
				if ($messageEl.hasClass('form__message')) {
					$messageEl.html('<div class="form__message--error banner-error">' + res.data.message + '</div>');
				} else {
					$messageEl.html(res.data.message);
				}
				$('html, body').animate({
					scrollTop: ($(res.data.target).parent().offset().top)
				}, 500);
			}


		},
		alertSuccess: function (res) {
			if (res.data.target !== '') {
				$(res.data.target).html('<div class="form__message--success">' + res.data.message + '</div>');
				$('html, body').animate({
					scrollTop: ($(res.data.target).parent().offset().top)
				}, 500);
			}
		},
		confirm: function (message, callback) {
			if (confirm(message)) {
				if (callback) {
					callback();
				}
			}
		}
		,
		/**
		 * Returns the function that you want to execute through its name.
		 * It returns undefined if the function || property doesn't exists
		 *
		 * @param functionName {String}
		 * @param context {Object || null}
		 */
		getFunctionByName: function (functionName, context) {
			if (typeof (window) == "undefined") {
				context = context || global;
			} else {
				context = context || window;
			}

			// Retrieve the namespaces of the function you want to execute
			var namespaces = functionName.split(".");

			var functionToExecute = namespaces.pop();

			for (var i = 0; i < namespaces.length; i++) {
				context = context[namespaces[i]];
			}

			// If the context really exists (namespaces), return the function or property
			if (context) {
				return context[functionToExecute];
			} else {
				return undefined;
			}
		},
		showClosedContent: function (res) {
			if (res.data.close_content !== undefined) {
				var $closeContentEl = $(res.data.close_content);
				$.each($closeContentEl, function () {
					if (!$(this).hasClass('close')) {
						$(this).addClass('close');
					}
				});
			}
			if (res.data.open_content !== undefined) {
				var $openContent = $(res.data.open_content);
				$.each($openContent, function () {
					if ($(this).hasClass('close')) {
						$(this).removeClass('close');
					}
				});
			}

		},
		paginatorProcess: function (res) {
			var $pagnatorEl;
			if (res.data.is_purchase === true) {
				$pagnatorEl = $('#sts-paginator-purchase');
			} else {
				$pagnatorEl = $('#sts-paginator');
			}
			var $pageEls = $pagnatorEl.find('.sts-paginator__item-numb');
			var $prevEl = $pagnatorEl.find('#sts-paginator__item--prev');
			var $nextEl = $pagnatorEl.find('#sts-paginator__item--next');

			$.each($pageEls, function () {
				$(this).removeClass('active');
				if (parseInt($(this).attr('data-page')) === parseInt(res.data.current_page)) {
					$(this).addClass('active');
				}
			});
			if (parseInt(res.data.current_page) === 1) {
				$prevEl.addClass('close');
			} else {
				$prevEl.removeClass('close');
			}
			if (parseInt(res.data.current_page) === parseInt(res.data.total_page)) {
				$nextEl.addClass('close');
			} else {
				$nextEl.removeClass('close');
			}
		},
		changeStatus: function (res) {
			var $statusEL = $(res.data.target_status);
			$statusEL.html(res.data.status);
			STS.showClosedContent(res);
			$(res.data.target_form_control).html(res.data.form_control_content);

		},
		asssignSelf: function (res) {
			var $supporterEl = $(res.data.target_supporter);
			$supporterEl.html(res.data.supporter);
			STS.showClosedContent(res);
		},
		settingEditor: function (res) {
			var $editorID = res.data.target;
			if (tinymce.get($editorID) !== null) {
				tinymce.get($editorID).setContent(res.data.content);
			}
			STS.showClosedContent(res);
		},
		showFormControlWebsite: function (res) {
			var $formWebEl = $(res.data.target_website);
			$formWebEl.html(res.data.content_web);
			STS.showClosedContent(res);
		},
		redirectAfterAlert: function (res) {
			if (res.data.url !== undefined) {
				setTimeout(function () {
					window.location.href = res.data.url;
				}, 3000);
			}
		},
		updateThemeStatus: function (res) {
			if (res.data.target_status !== undefined) {
				$(res.data.target_status).html(res.data.content_status);
			}
		},
		updateChartReport: function (res) {
			var nb_ticket_satisfied = res.data.nb_ticket_satisfied;
			var nb_ticket_unsatisfied = res.data.nb_ticket_unsatisfied;
			var nb_ticket_visited = res.data.nb_ticket_visited;
			var nb_ticket_other = res.data.nb_ticket_other;

			$(res.data.target_chart).html(res.data.content_chart);
			$(res.data.target_satisfied).html(res.data.content_satisfied);
			$(res.data.target_unsatisfied).html(res.data.content_unsatisfied);
			$(res.data.target_visited).html(res.data.content_visited);
			$(res.data.target_other).html(res.data.content_other);
			var $ctx = $('#reportChart');
			STS.reportChartInit($ctx, ['Unsatisfied', 'Satisfied', 'Visited not rating', 'Other'],
				[nb_ticket_unsatisfied, nb_ticket_satisfied, nb_ticket_visited, nb_ticket_other],
				[
					'rgba(255, 99, 132, 0.2)',
					'rgba(54, 162, 235, 0.2)',
					'#bf0000',
					'#09d261'
				], [
					'rgba(255, 99, 132, 1)',
					'rgba(54, 162, 235, 1)',
					'#bf0000',
					'#09d261'
				], false);
			STS.showClosedContent(res);
		},
		setCurrentPage: function (res) {
			$(res.data.target_current_page).val(res.data.current_page)
		},
		dashboardsUpdateFilter: function (res) {
			$(res.data.target_filter).html(res.data.content_filter);
			$(res.data.dropdown_toggle).dropdown('toggle');
		},
	};

	$document.ready(function () {
		STS.init();
	});
})(jQuery);