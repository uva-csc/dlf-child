/**
 * Fellows archive facet filter + card modal. Vanilla JS, no bundler.
 * Fetches /wp-json/dlf/v1/fellows once (dlfFellows.restUrl, localized from
 * functions.php), then does all filtering/rendering client-side — the
 * dataset is small (~250 rows) so this is instant with no per-filter
 * round trip. Progressively enhances the server-rendered grid that's
 * already in the page for no-JS/SEO/accessibility.
 */
( function () {
	'use strict';

	var grid = document.getElementById( 'dlf-fellow-grid-container' );
	var yearSelect = document.getElementById( 'dlf-facet-year' );
	var regionSelect = document.getElementById( 'dlf-facet-region' );
	var countrySelect = document.getElementById( 'dlf-facet-country' );
	var modal = document.getElementById( 'dlf-fellow-modal' );
	var modalContent = document.getElementById( 'dlf-fellow-modal-content' );
	var modalClose = document.getElementById( 'dlf-fellow-modal-close' );

	if ( ! grid || typeof dlfFellows === 'undefined' ) {
		return;
	}

	var fellows = [];

	function escapeHtml( str ) {
		var div = document.createElement( 'div' );
		div.textContent = str || '';
		return div.innerHTML;
	}

	// Read ?year=&region=&country= so single-fellow meta chips can deep-link
	// into a pre-filtered view of the archive.
	function paramsFromQuery() {
		var params = new URLSearchParams( window.location.search );
		if ( params.get( 'year' ) ) {
			yearSelect.value = params.get( 'year' );
		}
		if ( params.get( 'region' ) ) {
			regionSelect.value = params.get( 'region' );
		}
		if ( params.get( 'country' ) ) {
			countrySelect.value = params.get( 'country' );
		}
	}

	function renderCard( fellow ) {
		var img = fellow.headshot
			? '<img src="' + escapeHtml( fellow.headshot ) + '" alt="" loading="lazy">'
			: '';
		return (
			'<a class="dlf-fellow-card" href="' + escapeHtml( fellow.permalink ) + '" data-id="' + fellow.id + '">' +
				'<span class="dlf-fellow-card__image">' + img + '</span>' +
				'<span class="dlf-fellow-card__name">' + escapeHtml( fellow.name ) + '</span>' +
				'<span class="dlf-fellow-card__year">' + escapeHtml( fellow.year ) + '</span>' +
			'</a>'
		);
	}

	function render( list ) {
		if ( ! list.length ) {
			grid.innerHTML = '<p class="dlf-no-fellows">No fellows found.</p>';
			return;
		}
		grid.innerHTML = '<div class="dlf-fellow-grid">' + list.map( renderCard ).join( '' ) + '</div>';
	}

	function applyFilters() {
		var year = yearSelect.value;
		var region = regionSelect.value;
		var country = countrySelect.value;

		var filtered = fellows.filter( function ( f ) {
			if ( year && f.year !== year ) {
				return false;
			}
			if ( region && f.region !== region ) {
				return false;
			}
			if ( country && f.countries.indexOf( country ) === -1 ) {
				return false;
			}
			return true;
		} );

		render( filtered );
	}

	function openModal( fellow ) {
		var html = '';
		html += '<div class="dlf-fellow-modal__header">';
		if ( fellow.headshot ) {
			html += '<img src="' + escapeHtml( fellow.headshot ) + '" alt="">';
		}
		html += '<h2 class="dlf-fellow-modal__name">' + escapeHtml( fellow.name ) + '</h2>';
		html += '<p class="dlf-fellow-modal__meta">' + escapeHtml( fellow.year ) + ' Fellow</p>';
		if ( fellow.countries && fellow.countries.length ) {
			html += '<p class="dlf-fellow-modal__meta">' + escapeHtml( fellow.countries.join( ', ' ) ) + '</p>';
		}
		html += '</div>';

		html += '<div class="dlf-fellow-modal__body">';
		if ( fellow.leadership_vision ) {
			html += '<span class="dlf-fellow-modal__section-label">Leadership Vision</span>' + fellow.leadership_vision;
		}
		if ( fellow.project_description ) {
			html += '<span class="dlf-fellow-modal__section-label">Project Description</span>' + fellow.project_description;
		}
		if ( fellow.learn_more && fellow.learn_more.length ) {
			html += '<span class="dlf-fellow-modal__section-label">Learn More</span><ul class="dlf-fellow-modal__learn-more">';
			fellow.learn_more.forEach( function ( link ) {
				html += '<li><a href="' + escapeHtml( link.url ) + '" target="_blank" rel="noopener">' + escapeHtml( link.text ) + '</a></li>';
			} );
			html += '</ul>';
		}
		html += '<a class="dlf-fellow-modal__full-profile" href="' + escapeHtml( fellow.permalink ) + '">View full profile &rarr;</a>';
		html += '</div>';

		modalContent.innerHTML = html;
		modal.showModal();
	}

	grid.addEventListener( 'click', function ( event ) {
		var card = event.target.closest( '.dlf-fellow-card' );
		if ( ! card ) {
			return;
		}
		event.preventDefault();
		var id = parseInt( card.getAttribute( 'data-id' ), 10 );
		var fellow = fellows.filter( function ( f ) { return f.id === id; } )[ 0 ];
		if ( fellow ) {
			openModal( fellow );
		}
	} );

	if ( modalClose ) {
		modalClose.addEventListener( 'click', function () {
			modal.close();
		} );
	}
	if ( modal ) {
		modal.addEventListener( 'click', function ( event ) {
			if ( event.target === modal ) {
				modal.close();
			}
		} );
	}

	[ yearSelect, regionSelect, countrySelect ].forEach( function ( select ) {
		select.addEventListener( 'change', applyFilters );
	} );

	fetch( dlfFellows.restUrl )
		.then( function ( response ) { return response.json(); } )
		.then( function ( data ) {
			fellows = data;
			paramsFromQuery();
			applyFilters();
		} );
} )();
