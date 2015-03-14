var gameid = 0;

function toggleplay( id )
{
	var el = $( '#' + id );
	if (el.is( ':enabled' )) {
		el.prop( 'disabled', true );
	} else {
		el.prop( 'disabled', false );
	}
}

function togglewin( id )
{
}

function creategame( players ) {
	$.post('api.php', {
		action: 'creategame',
		players: players
	}, function( data ) {
		var i = 0, obj = $.parseJSON( data );

		gameid = parseInt( obj[0] );
		$( '#gameid' ).val( gameid );
		$( '#timestamp' ).val( ( obj[1] ) );
		$( '.play' ).prop( 'disabled', true );
		for (i = 1; i <= 6; i++) {
			if ($( '#play' + i ).is( ':checked' )) {
				$( '#win' + i ).prop( 'disabled', false );
			} else {
				$( '#' + i ).val( '' );
			}
		}
		$( '#creategame' ).prop( 'disabled', true );
		$( '#setwinners' ).prop( 'disabled', false );
	}).fail( function() {
		alert( "POST creategame failed." );
	});
}

function setwinner() {
	var i = 0, winner = 'nowinner', winnerspecies = 0;
	for (i = 1; i <= 6; i++) {
		if ($( '#win' + i ).is( ':checked' )) {
			winner = $( '#' + i ).val();
			winnerspecies = i;
			break;
		}
	}
	if (winnerspecies === 0) {
		alert( 'no winner' );
		return false;
	}
	$.post('api.php', {
		action: 'setwinner',
		gameid: gameid,
		winner: winner,
		winnerspecies: winnerspecies
	}, function( data ) {
		$( '#creategame' ).prop( 'disabled', false );
		$( '#setwinner' ).prop( 'disabled', true );
		$( '.win' ).prop( 'disabled', true );
		$( '.win' ).removeAttr( 'checked' );
		$( '.play' ).prop( 'disabled', false );
	}).fail( function() {
		alert( "POST setwinners failed." );
	});
}

function swiperight() {
	$( '#panner' ).animate( { right: '+=360' }, 100, function() {} )
}
function swipeleft() {
	$( '#panner' ).animate( { right: '-=360' }, 100, function() {} )
}

$( document ).ready( function() {
	var gameidlen = 0;

	$( '#creategame' ).click( function( e ) {
		var i = 0, player = '', players = [];
		gameid = 0;
		for (i = 1; i < 6; i++) {
			if ($( '#play' + i ).is( ':checked' )) {
				player = $( '#' + i ).val();
			} else {
				player = 'noplayer';
			}
			players.push( player );
		}
		creategame( players );
	} );
	$( '#gameid' ).on( 'keydown', function( event ) {
		if ((event.which > 47) && (event.which < 58)) {
			if (gameidlen < 3) {
				gameidlen++;
			} else {
				event.preventDefault();			
			}
		} else if ((event.which == 10) || (event.which == 13)) {
			// enter key
		} else if (event.which == 8) {
			gameidlen--;
		} else {
			event.preventDefault();			
		}
	} );
	$( '#statsplayer' ).on( 'keydown', function( event ) {
		if ((event.which == 10) || (event.which == 13)) {
			$.get('api.php', {
				action: 'getstats',
				player: $( '#statsplayer' ).val()
			}, function( data ) {
				retarr = $.parseJSON( data );
				$( '#output' ).val( 'Wins: ' + retarr[0]
					+ '\nMammal: ' + retarr[1]
					+ '\nReptile: ' + retarr[2]
					+ '\nBird: ' + retarr[3]
					+ '\nAmphibian: ' + retarr[4]
					+ '\nArachnid: ' + retarr[5]
					+ '\nInsect: ' + retarr[6] );
			}).fail( function() {
				alert( "GET stats failed." );
			});
		}
	} );
} );
