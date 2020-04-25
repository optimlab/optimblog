/**
 * @package    OptimBlog
 * @version    3.0.1.0
 * @author     Dmitriy Khokhlov <admin@optimlab.com>
 * @copyright  Copyright (c) 2018, Dmitriy Khokhlov. (http://optimlab.com/)
 * @license    https://opensource.org/licenses/GPL-3.0
 * @link       http://optimlab.com
 */
$(document).ready(function() {
	// Information List
	$('#information-list-view').click(function() {
		$('#content .information-layout > .clearfix').remove();

		$('#content .row > .information-layout').attr('class', 'information-layout col-xs-12');
		$('#content .row > .information-layout .thumbnail').attr('class', 'thumbnail media');
		$('#content .row > .information-layout .image').attr('class', 'image pull-left');
		$('#content .row > .information-layout .caption').attr('class', 'caption media-body');
		$('#information-block-view').removeClass('active');
		$('#information-grid-view').removeClass('active');
		$('#information-list-view').addClass('active');

		localStorage.setItem('information-display', 'list');
	});

	// Information Block
	$('#information-block-view').click(function() {
		$('#content .information-layout > .clearfix').remove();

		$('#content .row > .information-layout').attr('class', 'information-layout col-xs-12');
		$('#content .row > .information-layout .thumbnail').attr('class', 'thumbnail');
		$('#content .row > .information-layout .image').attr('class', 'image');
		$('#content .row > .information-layout .caption').attr('class', 'caption');
		$('#information-grid-view').removeClass('active');
		$('#information-list-view').removeClass('active');
		$('#information-block-view').addClass('active');

		localStorage.setItem('information-display', 'block');
	});

	// Information Grid
	$('#information-grid-view').click(function() {
		// What a shame bootstrap does not take into account dynamically loaded columns
		var cols = $('#column-right, #column-left').length;

		if (cols == 2) {
			$('#content .information-layout').attr('class', 'information-layout col-lg-6 col-md-6 col-sm-12 col-xs-12');
		} else if (cols == 1) {
			$('#content .information-layout').attr('class', 'information-layout col-lg-4 col-md-4 col-sm-6 col-xs-12');
		} else {
			$('#content .information-layout').attr('class', 'information-layout col-lg-3 col-md-3 col-sm-6 col-xs-12');
		}

		$('#content .row > .information-layout .thumbnail').attr('class', 'thumbnail');
		$('#content .row > .information-layout .image').attr('class', 'image');
		$('#content .row > .information-layout .caption').attr('class', 'caption');
		$('#information-block-view').removeClass('active');
		$('#information-list-view').removeClass('active');
		$('#information-grid-view').addClass('active');

		localStorage.setItem('information-display', 'grid');
	});

	if (localStorage.getItem('information-display') == 'block' || $('#content .row > .information-layout').hasClass('block')) {
		$('#information-block-view').trigger('click');
		$('#information-block-view').addClass('active');
	} else if (localStorage.getItem('information-display') == 'grid' || $('#content .row > .information-layout').hasClass('grid')) {
		$('#information-grid-view').trigger('click');
		$('#information-grid-view').addClass('active');
	} else {
		$('#information-list-view').trigger('click');
		$('#information-list-view').addClass('active');
	}

	/* Search Information */
	$('#search-information input[name=\'search_information\']').parent().find('button').on('click', function() {
		var url = $('base').attr('href') + 'index.php?route=information/search';

		var value = $('#search-information input[name=\'search_information\']').val();

		if (value) {
			url += '&search=' + encodeURIComponent(value);
		}

		location = url;
	});

	$('#search-information input[name=\'search_information\']').on('keydown', function(e) {
		if (e.keyCode == 13) {
			$('#search-information input[name=\'search_information\']').parent().find('button').trigger('click');
		}
	});
});
