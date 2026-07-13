=== Plogins Add-Ons - Product Options for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, product options, product addons, extra fields, custom product fields
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Wymaga wtyczek: woocommerce
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Dodaj opcje produktu WooCommerce, dodatki do produktów i niestandardowe pola produktu przed dodaniem do koszyka.

== Description ==

Dodatki pozwalają właścicielom sklepów oferować opcje produktu WooCommerce, dodatki do produktów i niestandardowe pola produktu, które klienci wybierają przed dodaniem produktu do koszyka: opakowanie na prezent, tekst grawerowany, przedłużoną gwarancję czy wybór koloru.

Dla każdego produktu definiujesz listę dodatków w edytorze produktów WooCommerce. Każdy dodatek ma etykietę, typ pola, opcjonalne oznaczenie jako wymagane oraz opcjonalną cenę.

* <strong>Typy pól</strong> — zwykły tekst, pole wyboru lub lista rozwijana.
* <strong>Limity tekstu</strong> — ustaw minimalną i maksymalną liczbę znaków dla tekstowych opcji produktu.
* <strong>Różnice cen</strong> — przypisz cenę dodatkowi (lub każdej opcji z listy); kwota jest automatycznie doliczana do sumy pozycji w koszyku.
* <strong>Bezpłatne lub płatne</strong> — pozostaw cenę na zero dla bezpłatnych opcji, takich jak spersonalizowana wiadomość.
* <strong>Wyświetlanie w koszyku i zamówieniu</strong> — dokonane wybory pojawiają się w koszyku, przy kasie i w zamówieniu.
* <strong>Ustawienia wyświetlania</strong> — wybierz nagłówek grupy, pokaż lub ukryj ceny opcji, włącz lub wyłącz gwiazdkę pola wymaganego i umieść opcje w karcie z obramowaniem, a wszystko to na stronie ustawień dodatków.

Definicje dodatków są przechowywane jako standardowa meta produktu, bez niestandardowych tabel w bazie danych, więc sama wtyczka pozostaje mała i szybka.

Ustawienia znajdziesz w <strong>WooCommerce → Dodatki</strong>. Usunięcie wtyczki czyści jej własne opcje; definicje poszczególnych produktów są zachowywane jako meta produktu, więc ponowna instalacja je przywraca.

Kod jest rozwijany otwarcie (open source) pod adresem https://github.com/wppoland/plogins-addons — to właśnie tam zgłosisz błąd lub zaproponujesz typ pola, który chcesz zobaczyć.

== Installation ==

1. Wgraj wtyczkę do `/wp-content/plugins/plogins-addons` lub zainstaluj przez Wtyczki → Dodaj nową.
2. Włącz ją. WooCommerce musi być aktywne.
3. Edytuj produkt, otwórz zakładkę <strong>Dodatki</strong> w panelu Dane produktu i dodaj swoje opcje.

== Frequently Asked Questions ==

= Documentation and links =

* <strong>Dokumentacja</strong> - https://plogins.com/pl/plogins-addons/docs/
* <strong>Strona wtyczki</strong> - https://plogins.com/pl/plogins-addons/
* <strong>Kod źródłowy</strong> - https://github.com/wppoland/plogins-addons
* <strong>Zgłoszenia błędów i propozycje funkcji</strong> - https://github.com/wppoland/plogins-addons/issues


= Does it require WooCommerce? =

Tak. WooCommerce musi być zainstalowane i aktywne.

= Where do customers see the options? =

Na stronie pojedynczego produktu, tuż nad przyciskiem Dodaj do koszyka. Dokonane wybory pojawiają się następnie w koszyku, przy kasie i w zamówieniu.

= What field types are included? =

Darmowa wersja zawiera pola tekstowe, pola wyboru i listy rozwijane. Każde pole może być bezpłatne lub doliczać kwotę do pozycji w koszyku.

= Can a product option change the price? =

Tak. Dodaj cenę do samego wiersza lub do poszczególnych opcji z listy, a wtyczka doliczy tę kwotę do pozycji w koszyku WooCommerce.

= Can I make a product option required? =

Tak. Zaznacz pole wyboru Wymagane przy danej opcji, a produktu nie będzie można dodać do koszyka, dopóki kupujący jej nie uzupełni.

= Can I limit text option length? =

Tak. Dodatki tekstowe mogą mieć minimalny i maksymalny limit znaków. Sklep wyświetla licznik na żywo, a serwer sprawdza te same limity przed dodaniem do koszyka.

= Does it create custom database tables? =

Nie. Definicje dodatków są przechowywane jako meta produktu.

= Does it support file uploads or conditional logic? =

To funkcje PRO. Add-Ons FREE skupia się na szybkich opcjach produktu: tekstowych, polach wyboru i listach rozwijanych.


= Does this plugin work on WordPress Multisite? =

Tak. Ta wtyczka jest zgodna z WordPress Multisite. Włącz ją dla całej sieci lub w pojedynczych witrynach; każda witryna zachowuje własne ustawienia i dane.

== Screenshots ==

1. W sklepie.
2. Ustawienia w panelu WordPress.
3. Na urządzeniu mobilnym.
== External Services ==

Add-Ons nie łączy się z żadną usługą zewnętrzną. Nie wysyła żadnych danych poza Twoją witrynę i nie ładuje zdalnych skryptów, czcionek ani modułów śledzących — pliki CSS/JS panelu i sklepu są serwowane z folderu wtyczki na Twoim własnym serwerze. Definicje dodatków są przechowywane jako meta produktu (`_addons_definitions`), a ustawienia wyświetlania w jednej opcji (`addons_settings`) — wszystko w Twojej bazie danych WordPress.

== Translations ==

Plogins Add-Ons zawiera polskie, niemieckie i hiszpańskie tłumaczenia interfejsu wtyczki. Domena tekstowa to `plogins-addons`, więc pakiety językowe z WordPress.org mogą również nadpisać lub rozszerzyć te dołączone tłumaczenia.

== Changelog ==

= 1.0.2 =
* Dodano dołączone polskie, niemieckie i hiszpańskie tłumaczenia interfejsu wtyczki.

= 1.0.1 =
* Pierwsza stabilna wersja.

= 0.3.1 =
* Zmieniono nazwę na Plogins Add-Ons dla WooCommerce, aby nazwa wtyczki była bardziej charakterystyczna.

= 0.3.0 =
* Dodano minimalny i maksymalny limit znaków dla dodatków tekstowych, z licznikami w sklepie i weryfikacją po stronie serwera.

= 0.2.0 =
* Dodano konfigurowalny nagłówek grupy wyświetlany nad polami dodatków na stronie produktu.
* Dodano ustawienia wyświetlania: pokazywanie/ukrywanie cen opcji, przełącznik gwiazdki pola wymaganego oraz opcjonalny styl karty z obramowaniem.
* Dodano procedurę dezinstalacji, która usuwa własne opcje wtyczki (definicje produktów są zachowywane jako meta produktu).

= 0.1.0 =
* Pierwsze wydanie.
