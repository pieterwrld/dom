/**
 * Beheerscherm: mediakiezer, herhaalvelden en sleepvolgorde.
 */
(function ($) {
  'use strict';

  var L = window.domContentL10n || {};

  /* ============ MEDIA ============ */

  function vulMedia($media, attachment) {
    var $vak = $media.find('.domc-media__vak');
    var $preview = $media.find('.domc-media__preview');
    var src = '';

    if (attachment) {
      if (attachment.sizes && attachment.sizes.medium) {
        src = attachment.sizes.medium.url;
      } else if (attachment.sizes && attachment.sizes.thumbnail) {
        src = attachment.sizes.thumbnail.url;
      } else if (attachment.type === 'image') {
        src = attachment.url;
      } else if (attachment.image && attachment.image.src) {
        src = attachment.image.src;
      }
    }

    $media.find('.domc-media__id').val(attachment ? attachment.id : '');
    $media.find('.domc-media__naam').text(attachment ? (attachment.filename || '') : '');
    $media.find('.domc-media__kies').text(attachment ? (L.vervangen || 'Vervangen') : (L.kiezen || 'Kiezen'));
    $media.find('.domc-media__wis').prop('hidden', !attachment);
    $vak.toggleClass('is-gevuld', !!attachment);

    if (src) {
      $preview.html($('<img>', { src: src, alt: '' }));
    } else if (attachment) {
      $preview.html('<span class="domc-media__film dashicons dashicons-video-alt3"></span>');
    } else {
      $preview.empty();
    }
  }

  $(document).on('click', '.domc-media__kies', function (e) {
    e.preventDefault();
    var $media = $(this).closest('.domc-media');
    var type = $media.data('type') === 'video' ? 'video' : 'image';

    var frame = wp.media({
      title: type === 'video' ? (L.kiesVideo || 'Video kiezen') : (L.kiesAfbeelding || 'Afbeelding kiezen'),
      button: { text: L.gebruiken || 'Gebruiken' },
      library: { type: type },
      multiple: false
    });

    frame.on('select', function () {
      vulMedia($media, frame.state().get('selection').first().toJSON());
    });

    frame.open();
  });

  $(document).on('click', '.domc-media__wis', function (e) {
    e.preventDefault();
    vulMedia($(this).closest('.domc-media'), null);
  });

  /* ============ HERHAALVELDEN ============ */

  // De namen van de invoervelden dragen een index. Na toevoegen, verwijderen of
  // slepen moeten die weer op volgorde staan, anders overschrijven rijen elkaar
  // bij het opslaan.
  function hernummer($rep) {
    var basis = $rep.data('naam');
    $rep.find('> .domc-rep__rijen > .domc-rij').each(function (i) {
      $(this).find('[name]').each(function () {
        var naam = $(this).attr('name');
        if (naam.indexOf(basis) !== 0) { return; }
        var rest = naam.slice(basis.length);
        $(this).attr('name', basis + rest.replace(/^\[[^\]]*\]/, '[' + i + ']'));
      });
    });
  }

  $(document).on('click', '.domc-rep__toevoegen', function (e) {
    e.preventDefault();
    var $rep = $(this).closest('.domc-rep');
    var sjabloon = $rep.find('> .domc-rep__sjabloon').html() || '';
    var index = $rep.find('> .domc-rep__rijen > .domc-rij').length;

    // De blauwdruk gebruikt __i__ als plaatsaanduiding voor het rijnummer.
    var $nieuw = $(sjabloon.split('__i__').join(String(index)));
    $rep.find('> .domc-rep__rijen').append($nieuw);
    hernummer($rep);
    $nieuw.find('input[type="text"]').first().trigger('focus');
  });

  $(document).on('click', '.domc-rij__weg', function (e) {
    e.preventDefault();
    if (!window.confirm(L.weetJeHetZeker || 'Deze rij verwijderen?')) { return; }
    var $rep = $(this).closest('.domc-rep');
    $(this).closest('.domc-rij').remove();
    hernummer($rep);
  });

  // De koptekst van een rij volgt het veld dat in het schema is aangewezen.
  $(document).on('input', '.domc-rij__body input[type="text"]', function () {
    var $rij = $(this).closest('.domc-rij');
    if ($(this).closest('.domc-rij__body').find('input[type="text"]').first()[0] !== this) { return; }
    $rij.find('> .domc-rij__kop .domc-rij__titel').text($(this).val() || (L.nieuw || 'Nieuw'));
  });

  $(function () {
    $('.domc-rep').each(function () {
      var $rep = $(this);
      $rep.find('> .domc-rep__rijen').sortable({
        handle: '.domc-rij__greep',
        placeholder: 'domc-rij is-sleep',
        forcePlaceholderSize: true,
        axis: 'y',
        update: function () { hernummer($rep); }
      });
    });
  });
})(jQuery);
