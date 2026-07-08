=== Plogins Add-Ons - Product Options for WooCommerce ===
Contributors: motylanogha
Tags: woocommerce, product options, product addons, extra fields, custom product fields
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 8.1
Wymaga wtyczek: woocommerce
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Dodaj opcje produktu WooCommerce, dodatki do produktów i niestandardowe pola produktów przed dodaniem do koszyka.

== Description ==

Dodatki umożliwiają właścicielom sklepów oferowanie opcji produktów WooCommerce, dodatków do produktów i niestandardowych pól produktów, które klienci wybierają przed dodaniem produktu do koszyka: opakowanie prezentu, grawerowana wiadomość, przedłużona gwarancja lub wybór koloru.

Dla każdego produktu definiujesz listę dodatków w edytorze produktów WooCommerce. Każdy dodatek ma etykietę, typ pola, opcjonalną wymaganą flagę i opcjonalną cenę.

* <strong>Typy pól</strong>, zwykły tekst, pole wyboru lub menu wyboru.
* <strong>Limity tekstu</strong>, ustaw minimalną i maksymalną długość znaków dla opcji produktów tekstowych.
* <strong>Delty cen</strong>, podaj cenę dla dodatku (lub każdej wybranej opcji); kwota ta jest automatycznie dodawana do sumy koszyka.
* <strong>Bezpłatne lub płatne</strong>, pozostaw cenę na poziomie zerowym, aby uzyskać bezpłatne opcje, takie jak spersonalizowana wiadomość.
* <strong>Wyświetlanie koszyka i zamówień</strong>, wybory klienta pojawiają się w koszyku, przy kasie i w zamówieniu.
* <strong>Ustawienia wyświetlania</strong>, wybierz nagłówek grupy, pokaż lub ukryj ceny opcji, przełącz gwiazdkę wymaganego pola i zawiń opcje w obramowaną kartę – wszystko to na stronie ustawień dodatków.

Definicje dodatków są przechowywane jako standardowa meta produktu, bez niestandardowych tabel bazy danych, więc sama wtyczka pozostaje mała i szybka.

Ustawienia znajdziesz w <strong>WooCommerce → Dodatki</strong>. Usunięcie wtyczki powoduje wyczyszczenie jej własnych opcji; definicje poszczególnych produktów są przechowywane jako meta produktu, więc ponowna instalacja przywraca je.

Kod jest rozwijany jako otwarty pod adresem https://github.com/wppoland/plogins-addons. To miejsce, w którym możesz zgłosić błąd lub zasugerować typ pola, który chciałbyś zobaczyć.

== Installation ==

1. Prześlij wtyczkę do `/wp-content/plugins/plogins-addons` lub zainstaluj poprzez Wtyczki → Dodaj nową.
2. Aktywuj. WooCommerce musi być aktywny.
3. Edytuj produkt, otwórz zakładkę <strong>Dodatki</strong> w panelu Dane produktu i dodaj swoje opcje.

== Frequently Asked Questions ==

= Documentation and links =

* <strong>Dokumentacja</strong> - https://plogins.com/pl/plogins-addons/docs/
* <strong>Strona wtyczki</strong> - https://plogins.com/pl/plogins-addons/
* <strong>Kod źródłowy</strong> - https://github.com/wppoland/plogins-addons
* <strong>Raporty o błędach i prośby o nowe funkcje</strong> - https://github.com/wppoland/plogins-addons/issues


= Does it require WooCommerce? =

Tak. WooCommerce musi być zainstalowany i aktywny.

= Where do customers see the options? =

Na stronie pojedynczego produktu, tuż nad przyciskiem Dodaj do koszyka. Wybrane przez nich produkty pojawiają się następnie w koszyku, przy kasie i w zamówieniu.

= What field types are included? =

Darmowa wersja zawiera pola tekstowe, pola wyboru i listy rozwijane wyboru. Każde pole może być bezpłatne lub dodać cenę do linii koszyka.

= Can a product option change the price? =

Tak. Dodaj cenę do samego wiersza lub do poszczególnych wybranych opcji, a Add-Ons doda tę kwotę do linii koszyka WooCommerce.

= Can I make a product option required? =

Tak. Zaznacz pole wyboru Wymagane przy danej opcji, a produktu nie można dodać do koszyka, dopóki kupujący go nie uzupełni.

= Can I limit text option length? =

Tak. Dodatki tekstowe mogą mieć minimalny i maksymalny limit znaków. Witryna sklepu wyświetla licznik na żywo, a serwer sprawdza te same limity przed dodaniem do koszyka.

= Does it create custom database tables? =

Nie. Definicje dodatków są przechowywane jako meta produktu.

= Does it support file uploads or conditional logic? =

To są funkcje PRO. Dodatki FREE skupiają się na szybkim tekście, polach wyboru i wyborze opcji produktu.


= Does this plugin work on WordPress Multisite? =

Tak. Ta wtyczka jest kompatybilna z WordPress Multisite. Aktywuj go w sieci lub aktywuj na poszczególnych stronach; każda witryna przechowuje własne ustawienia i dane.

== Screenshots ==

1. Na wystawie sklepowej.
2. Ustawienia w panelu administracyjnym WordPress.
3. Na urządzeniu mobilnym.
== External Services ==

Dodatki nie łączą się z żadnymi usługami zewnętrznymi. Nie wysyła żadnych danych z Twojej witryny i nie ładuje żadnych zdalnych skryptów, czcionek ani modułów śledzących. CSS/JS panelu administracyjnego i witryny sklepowej są obsługiwane z folderu wtyczek na Twoim własnym serwerze. Definicje dodatków są przechowywane jako meta produktu („_addons_definitions”), a ustawienia wyświetlania w jednej opcji („addons_settings”), a wszystko to przechowywane w bazie danych WordPress.

== Changelog ==

= 1.0.1 =
* Pierwsza stabilna wersja.

= 0.3.1 =
* Zmieniono nazwę na Dodatki Plogins dla WooCommerce, aby uzyskać bardziej charakterystyczną nazwę wtyczki.

= 0.3.0 =
* Dodaj minimalne i maksymalne limity znaków dla dodatków tekstowych, z licznikami w sklepie i weryfikacją po stronie serwera.

= 0.2.0 =
* Dodaj konfigurowalny nagłówek grupy pokazany nad polami dodatkowymi na stronie produktu.
* Dodaj ustawienia wyświetlania: pokaż/ukryj ceny opcji, przełącz gwiazdkę wymaganego pola i opcjonalny styl karty z obramowaniem.
* Dodaj procedurę dezinstalacji, która usuwa własne opcje wtyczki (definicje produktów są zachowywane jako meta produktu).

= 0.1.0 =
* Pierwsze wydanie.
