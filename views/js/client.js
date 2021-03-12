// ------------------ VARIABLES
var nbr_promotions = (LP_CONFIGURATION.lp_promotions_nbr) ? LP_CONFIGURATION.lp_promotions_nbr : 3;
var nbr_messages = (LP_CONFIGURATION.lp_messages_nbr) ? LP_CONFIGURATION.lp_messages_nbr : 3;
var counter = 0;

var startNotif;
var sendSocket;
var Gcurrentuser = usersocket;
var Gcurrentusername = usersocketname;
var client = (function () {
	var socket = io.connect(lp_server_node, { secure: true });
	var uid = Gcurrentuser;
	function init() {
		getSocket();
		startNotif(uid);
	}

	startNotif = function (id) {
		socket.emit("login", {
			userID: id,
			customer_name: Gcurrentusername
		});
	};

	socket.on('disconnected', function () {
	});

	function getSocket() {

		socket.on('actionvalidateorder', function (data) {
			// console.log(data);
			// setTimeout(function(){
			$('.lp_comments_details').append(chat_order_html(data));
			$('.lp_side_chat_container').append(side_chat_message_html(data, 'order'));
			chat_update_scrollbar();
			chat_show_particales();
			// }, 500);

		});

		socket.on('actioncartsavenew', function (data) {

			// console.log(data); 
			// setTimeout(function(){
			$('.lp_comments_details').append(chat_product_html(data));
			$('.lp_side_chat_container').append(side_chat_message_html(data, 'product'));
			chat_update_scrollbar();
			chat_show_particales();
			// }, 500);

		});


		socket.on('actionlikedproduct', function (data) {
			// console.log(data);
			// setTimeout(function(){
			$('.lp_comments_details').append(chat_like_product_html(data));
			$('.lp_side_chat_container').append(side_chat_message_html(data, 'like'));
			get_state();
			chat_update_scrollbar();
			chat_show_particales('love');
			// }, 500);

		});

		socket.on('actionauthentication', function (data) {
			// console.log(data);
			// setTimeout(function(){
			$('.lp_comments_details').append(chat_customer_html(data));
			$('.lp_side_chat_container').append(side_chat_message_html(data, 'customer'));
			get_state();
			chat_update_scrollbar();
			chat_show_particales();
			// }, 500);

		});

		socket.on('actioncustomeraccountadd', function (data) {
			// console.log(data);
			// setTimeout(function(){
			$('.lp_comments_details').append(chat_customer_html(data));
			$('.lp_side_chat_container').append(side_chat_message_html(data, 'customer'));
			get_state();
			chat_update_scrollbar();
			chat_show_particales();
			// }, 500);

		});

		socket.on('actionafterdeleteproductincart', function (data) { });

		socket.on('userlogged', function (data) {
			// console.log(data);
			$('.lp_comments_details').append(chat_customer_html(data));
			get_state();
			chat_show_particales();
		});

		socket.on('getpromonotification', function (data) {
			// console.log(data);
			chat_promo_html(data.promo);
			side_chat_promo_html(data.promo);
		});

		socket.on('getscheduled_product', function (data) {
			// console.log(data);
			chat_scheduled_product_html(data.scheduled_product);
			chat_update_scrollbar();
		});
	}

	sendSocket = function (event = "default", data = {}) {
		socket.emit(event, data);
	}

	init();
	return;
})();


// ------------------ FUNCTIONS
function get_state() {
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: baseUri + 'index.php',
		data: {
			ajax: 1,
			token: lp_token,
			controller: 'page',
			fc: 'module',
			module: 'lnk_livepromo',
			action: 'getStateLP',
		}
	}).fail(function (jqXHR, textStatus) {
		console.log(jqXHR);
		console.log(textStatus);
	}).success(function (data) {
		$('.lp-label.orders').html(data.order_number);
		$('.lp-label.carts').html(data.cart_number);
		$('.lp-label.users span').html(data.customer_connected);
	});
}

function get_current_time() {
	var today = new Date();
	// var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
	// var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
	var time = today.getHours() + ":" + today.getMinutes();
	return time;
}

function get_customer_name(data) {
	var customer_name = "New user";
	if (data.customer_name) {
		customer_name = data.customer_name;
	}

	return "<strong>" + customer_name + "</strong>&nbsp";
}


// ------------------ LIVE CHAT FUNCTIONS

function chat_show_particales(type = 'like') {
	$(`.lp_interactive_btn.${type}`).trigger('click');
}

function chat_update_scrollbar($added_height = 0) {

	var $scrollbar = window.Scrollbar;
	const $scrollbarElm = $scrollbar.init(document.querySelector('.lp_body_container'), {
		thumbMinSize: 4,
	});
	var $scroll = 500;
	var $scrollbarSize = $scrollbarElm.getSize();
	$scroll = ($scrollbarSize.content.height - $scrollbarSize.container.height) - $added_height;

	$scrollbarElm.scrollTo(0, $scroll, 600, {
		callback: () => console.log('done!'),
	});

}

function chat_preserve_messages(messages_nbr = nbr_messages) {

	var n = $(".lp_side .lp_comment_details_container").length;

	if (n > messages_nbr) {
		var nd = n - messages_nbr;
		$('.lp_side .lp_comments_details').find(".lp_comment_details_container").slice(1, nd).remove();
	}

}

function chat_preserve_promos(promotions_nbr = nbr_promotions) {

	var n = $(".lp_side .lp_promo").length;

	if (n > promotions_nbr) {
		var nd = n - promotions_nbr;
		$('.lp_side .lp_promos_container').find(".lp_promo").slice(1, nd).remove();
	}

}

// ------------------ SIDE LIVE CHAT FUNCTIONS

function side_chat_preserve_messages(messages_nbr = nbr_messages) {

	var n = $(".lp_side_chat .lp_action").length;

	if (n > messages_nbr) {
		var nd = n - messages_nbr;
		$('.lp_side_chat .lp_side_chat_container').find(".lp_action").slice(1, nd).remove();
	}

}

function side_chat_hide() {
	$('.lp_side_chat .lp_action.open').each(function () {
		$(this).removeClass('open');
	});
}

// ------------------ LIVE CHAT HTML

function chat_promo_html($promo) {

	chat_preserve_promos();

	$promo_html = `
        <div class="lp_promo">
            <div class="lp_promo_content">
                <span>${$promo.name} </span>
                <p class="code_promo" data-clipboard-text="${$promo.code}">${$promo.code}</p> 
            </div>
            <div class="close_promo"> FERMER </div>
        </div>
    `;

	$('.lp_promos_container').append($promo_html);

}

function chat_scheduled_product_html($scheduled_product) {

	$scheduled_product_image_html = `
        <div class="image" style="background: url('${$scheduled_product.image_url}') no-repeat 50% 0% / cover;"></div>
    `;

	$scheduled_product_title_html = `
        <a href="#" class="lp-label day_product"> Produit du jour </a>
        <h3>${$scheduled_product.name}</h3>
    `;

	$('.lp_feature_product_image').html($scheduled_product_image_html);
	$('.lp_feature_product_title_text').html($scheduled_product_title_html);

}

function skip_message(data, PID = null) {

	
	if (PID) {

		console.log(".PID-" + PID + ".ID-" + data.id); 
		console.log($(".PID-" + PID + ".ID-" + data.id).length);

		if ($(".PID-" + PID + ".ID-" + data.id).length)
			return true;

	}else {
		console.log(".ID-" + data.id);
		console.log($(".ID-" + data.id).length);

		if ($(".ID-" + data.id).length) 
			return true;
	}

	return false;
}

function chat_product_html(data) {

	var product = data.productdetail.lang[1];
	var specific_price = data.productdetail.specific_price[lp_group_id];

	chat_preserve_messages();

	if (skip_message(data, product.id))
		return '';
	else
		return `
			<div class="lp_comment_details_container PID-${product.id} ID-${data.id} div-${data.iddiv}">
				<div class="lp_comment">
					<div class="lp_comment_details">

						<div class="lp_profile_img">
							<img src="${product.image}" alt="">
						</div>
						<div class="lp_comment_info_container">
							<div class="lp_comment_info">
								<div class="lp_customer_name">
									<a href="#">${get_customer_name(data)}</a>
									<span class="lp_dot">&nbsp;·&nbsp;</span>
									<span class="lp_time">${get_current_time()}</span>
								</div>

								<div class="lp_product_details"> 
									<a class="lp_product_img" href="${product.url}">
										<img src="${product.image}">
									</a> 
									<div class="lp_product_name">
										${product.name}
										<span class="action">
											<span>${product.action}</span>&nbsp;
											<span class="${product.extra_action}"></span> 
										</span>  
									</div>
								</div>

								<div class="lp_product_price">
									<span class="lp_price product-price">${specific_price}</span>
									<span class="lp_old_price product-price">${product.price}</span>
								</div>

								<div class="lp_like_container">
									<a href="${product.url}" class="like">
										<img src="${lp_url_svg}cart-white.svg">
									</a>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		`;

}

function chat_like_product_html(data) {

	var product = data.productdetail.lang[1];

	chat_preserve_messages();

	if (skip_message(data, product.id))
		return '';
	else
		return ` 
		<div class="lp_comment_details_container PID-${product.id} ID-${data.id} div-${data.iddiv}">
			<div class="lp_comment liked">
				<div class="lp_comment_details">

					<div class="lp_comment_info_container">
						<div class="lp_comment_info">
								
							<div class="lp_product_details">
								<a class="lp_product_img" href="${product.url}">
									<img src="${product.image}">
								</a>
								<div class="lp_product_name"> 
									<span>
										<strong>${get_customer_name(data)}</strong> 
										${product.action} 
										<span class="${product.extra_action}"></span> 
										${product.name}
									</span>
								</div>
							</div>

							<div class="lp_like_container">
								<a href="${product.url}index.php?controller=cart?add=1&id_product=${product.id}" class="like">
									<img src="${lp_url_svg}cart-white.svg">
								</a>
							</div>

						</div>
					</div>

				</div>
			</div>
		</div>
	`;
}

function chat_customer_html(data) {

	chat_preserve_messages();

	if (skip_message(data))
		return '';
	else
		return `
			<div class="lp_comment_details_container ID-${data.id} div-${data.iddiv}">
				<div class="lp_comment customer">
					<div class="lp_comment_details">
						<div class="lp_comment_info_container">
							<div class="lp_comment_info">
								<div class="lp_product_details">
									<div class="lp_product_name">
										${get_customer_name(data)} 
										<span>${newuser}</span>
										<span class="celebrate"></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		`;

}

function chat_order_html(data) {

	chat_preserve_messages();
	var product = data.productdetail.lang[1];

	if (skip_message(data))
		return '';
	else
		return `
			<div class="lp_comment_details_container ID-${data.id} div-${data.iddiv}">
				<div class="lp_comment customer order"> 
					<div class="lp_comment_details">

						<div class="lp_comment_info_container">
							<div class="lp_comment_info">
								<div class="lp_product_details">
									<div class="lp_product_name">
										<strong>${get_customer_name(data)} </strong>&nbsp;
										<span>${product.action}</span>&nbsp;
										<span class="celebrate"></span> 
										<span class="celebrate"></span>
									</div>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		`;

}

// ------------------ SIDE LIVE CHAT HTML

function side_chat_promo_html($promo) {

	side_chat_hide();
	side_chat_preserve_messages();

	$promo_html = `
        <div class="lp_action promo">
            <div class="lp_action_info_container">
                <div class="lp_action_info">
                    <div class="lp_action_product_details">
                        <div class="lp_action_product_name">
                            <span>${$promo.name} </span>
                            <p class="code_promo" data-clipboard-text="${$promo.code}">${$promo.code}</p> 
                        </div>
                        <a class="lp_action_product_img gift_box_link" href="#${$promo.name}"><span class="gift_box"></span></a>
                    </div>
                </div>
            </div>
        </div>
    `;

	$('.lp_side_chat_container').append($promo_html);

}

function side_chat_message_html(data, action = 'product') {

	side_chat_hide();
	side_chat_preserve_messages();

	var PID = "";
	var css_class;
	var product = data.productdetail.lang[1];
	var name = `<span><strong>${get_customer_name(data)}</strong>`;
	var link = `<a class="lp_action_product_img celebrate_link" href="#"><span class="celebrate"></span></a>`;

	switch (action) {
		case 'product':
			PID = product.id;
			css_class = "ID-" + data.id + " PID-" + product.id;
			name = `${product.name} <span>${product.action}</span>`;
			link = `<a class="lp_action_product_img" href="${product.url}"><img src="${product.image}"></a>`;
			break;

		case 'customer':
			css_class = "ID-" + data.id;
			break;

		case 'like':
			PID = product.id;
			css_class = "ID-" + data.id + " PID-" + product.id;
			name += ` ${product.action} <span class="${product.extra_action}"></span> ${product.name}</span>`;
			break;

		case 'order':
			css_class = "ID-" + data.id;
			name += ` ${userdoorder}`;
			break;


		default:
			break;
	}

	if (skip_message(data, PID))
		return '';
	return `
			<div class="lp_action ${css_class}" id="lp_action_${data.iddiv}">
				<div class="lp_action_info_container">
					<div class="lp_action_info">
						<div class="lp_action_product_details"> 
							<div class="lp_action_product_name"> 
								${name}
							</div>
							${link}
						</div>
					</div>
				</div>
			</div>
		`;

}

