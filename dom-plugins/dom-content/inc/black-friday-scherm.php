<?php
/**
 * Het Black Friday-scherm: één knop aan, één knop uit.
 *
 * Het afprijzen gebeurt in porties, anders loopt een winkel van een paar
 * honderd producten tegen de tijdslimiet van de server aan. De voortgangsbalk
 * hieronder roept de REST-route net zo vaak aan tot alles is gehad.
 */

defined('ABSPATH') || exit;

function dom_content_render_black_friday() {
    $actief    = dom_bf_is_actief();
    $kortingen = dom_bf_kortingen();
    $totaal    = count(dom_bf_product_ids());
    ?>
    <section class="domc-kaart">
        <header class="domc-kaart__kop">
            <h2>
                <?php esc_html_e('Status', 'dom-content'); ?>
                <span class="domc-bf-status <?php echo $actief ? 'aan' : 'uit'; ?>">
                    <?php echo $actief ? esc_html__('Actie staat AAN', 'dom-content') : esc_html__('Actie staat UIT', 'dom-content'); ?>
                </span>
            </h2>
            <p>
                <?php
                printf(
                    /* translators: %d is het aantal producten. */
                    esc_html__('%d producten vallen in een categorie met korting.', 'dom-content'),
                    (int) $totaal
                );
                ?>
            </p>
        </header>
        <div class="domc-kaart__body">
            <table class="domc-bf-tabel">
                <thead><tr><th><?php esc_html_e('Categorie', 'dom-content'); ?></th><th><?php esc_html_e('Korting', 'dom-content'); ?></th></tr></thead>
                <tbody>
                <?php foreach ($kortingen as $slug => $pct) :
                    $term = get_term_by('slug', $slug, 'product_cat');
                ?>
                    <tr>
                        <td><?php echo esc_html($term ? $term->name : $slug); ?></td>
                        <td><b><?php echo (int) $pct; ?>%</b></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <p class="domc-help" style="margin-top:14px">
                <?php esc_html_e('Categorieën die hier niet in staan krijgen geen korting. Een product dat in twee categorieën met een verschillend percentage zit, slaan we over en melden we na afloop.', 'dom-content'); ?>
            </p>

            <div class="domc-bf-knoppen">
                <button type="button" class="button button-primary button-hero" id="domBfAan" <?php disabled($actief); ?>>
                    <?php esc_html_e('Kortingen doorvoeren', 'dom-content'); ?>
                </button>
                <button type="button" class="button button-hero" id="domBfUit" <?php disabled(!$actief); ?>>
                    <?php esc_html_e('Kortingen terugdraaien', 'dom-content'); ?>
                </button>
                <button type="button" class="button" id="domBfTest">
                    <?php esc_html_e('Eerst proefdraaien', 'dom-content'); ?>
                </button>
            </div>

            <div class="domc-bf-balk" id="domBfBalk" hidden><i></i></div>
            <pre class="domc-bf-uit" id="domBfUitslag" hidden></pre>

            <p class="domc-help">
                <?php esc_html_e('Terugdraaien zet alleen terug wat deze actie heeft gezet. Is een prijs daarna met de hand aangepast, dan blijft die staan en wordt hij gemeld.', 'dom-content'); ?>
            </p>
        </div>
    </section>

    <script>
    (function () {
        var rest  = <?php echo wp_json_encode(esc_url_raw(rest_url('dom/v1/black-friday'))); ?>;
        var nonce = <?php echo wp_json_encode(wp_create_nonce('wp_rest')); ?>;

        var balk = document.getElementById('domBfBalk');
        var uit  = document.getElementById('domBfUitslag');
        var knoppen = ['domBfAan', 'domBfUit', 'domBfTest'].map(function (id) { return document.getElementById(id); });

        function samenvoegen(a, b) {
            ['producten','varianten','overgeslagen','zonder_prijs','al_lager','handmatig_gewijzigd','aan_sale_toegevoegd','uit_sale_gehaald'].forEach(function (k) {
                a[k] = (a[k] || 0) + (b[k] || 0);
            });
            ['conflicten','anders','voorbeelden'].forEach(function (k) {
                a[k] = (a[k] || []).concat(b[k] || []);
            });
            Object.keys(b.per_categorie || {}).forEach(function (c) {
                if (!a.per_categorie[c]) { a.per_categorie[c] = b.per_categorie[c]; }
                else { a.per_categorie[c].varianten += b.per_categorie[c].varianten; }
            });
            return a;
        }

        function draai(actie) {
            knoppen.forEach(function (k) { if (k) k.disabled = true; });
            balk.hidden = false;
            uit.hidden = false;
            uit.textContent = 'Bezig…';
            var totaal = { per_categorie: {} };

            function portie(offset) {
                return fetch(rest, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
                    body: JSON.stringify({ actie: actie, offset: offset, limit: 20 })
                }).then(function (r) { return r.json(); }).then(function (d) {
                    if (!d || !d.ok) { throw new Error((d && d.fout) || 'Er ging iets mis'); }
                    samenvoegen(totaal, d.telling);
                    var pct = Math.round(Math.min(100, ((d.offset + 20) / d.totaal) * 100));
                    balk.firstElementChild.style.width = pct + '%';
                    uit.textContent = 'Bezig… ' + pct + '%  (' + (totaal.producten || 0) + ' producten, ' + (totaal.varianten || 0) + ' prijzen)';
                    if (!d.klaar) { return portie(d.volgende); }
                    return d;
                });
            }

            portie(0).then(function () {
                var r = [];
                r.push('Klaar.');
                r.push('Producten geraakt: ' + (totaal.producten || 0));
                r.push('Prijzen gezet: ' + (totaal.varianten || 0));
                if (totaal.aan_sale_toegevoegd) r.push('Aan de Sale-collectie toegevoegd: ' + totaal.aan_sale_toegevoegd);
                if (totaal.uit_sale_gehaald) r.push('Uit de Sale-collectie gehaald: ' + totaal.uit_sale_gehaald);
                if (totaal.al_lager) r.push('Had al een hogere korting, ongemoeid gelaten: ' + totaal.al_lager);
                if (totaal.zonder_prijs) r.push('Zonder bruikbare normale prijs: ' + totaal.zonder_prijs);
                if (totaal.handmatig_gewijzigd) r.push('Na de actie met de hand aangepast, niet teruggezet: ' + totaal.handmatig_gewijzigd);
                if ((totaal.conflicten || []).length) {
                    r.push('');
                    r.push('Overgeslagen, zit in twee categorieën met een ander percentage:');
                    totaal.conflicten.forEach(function (c) { r.push('  - ' + c.naam + ' (' + c.slugs.join(', ') + ')'); });
                }
                uit.textContent = r.join('\n');
                setTimeout(function () { location.reload(); }, 2500);
            }).catch(function (e) {
                uit.textContent = 'Mislukt: ' + e.message;
                knoppen.forEach(function (k) { if (k) k.disabled = false; });
            });
        }

        document.getElementById('domBfAan').addEventListener('click', function () {
            if (window.confirm('De prijzen van alle producten in deze categorieën worden aangepast. Doorgaan?')) { draai('aan'); }
        });
        document.getElementById('domBfUit').addEventListener('click', function () {
            if (window.confirm('De actieprijzen worden teruggedraaid. Doorgaan?')) { draai('uit'); }
        });
        document.getElementById('domBfTest').addEventListener('click', function () { draai('rapport'); });
    })();
    </script>
    <?php
}
