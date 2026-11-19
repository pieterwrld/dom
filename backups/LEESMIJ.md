# Prijsback-ups

Momentopnamen van de WooCommerce-prijzen, gemaakt vlak voor een actie die
prijzen in bulk aanpast. Elke regel is één prijsdrager: een eenvoudig product
of één variant van een product met maten.

| veld | betekenis |
|---|---|
| `id` | post-id van het product of de variant |
| `ouder` | post-id van het product (bij een variant de ouder) |
| `naam` | productnaam op het moment van de opname |
| `regular` | normale prijs |
| `sale` | aanbiedingsprijs, leeg als die er niet was |

Deze bestanden zijn een vangnet naast de meta die het terugdraaien gebruikt.
Het terugdraaien in De Outfitman > Black Friday heeft ze niet nodig.
