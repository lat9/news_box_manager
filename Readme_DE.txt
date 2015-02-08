=================================================
| News Box Manager Version 1.4 deutsch/englisch |
|                                               |
| basiert auf dem News Box Manager 1.3.0        |
| modifiziert von MaleBorg <maleborg@gmx.de>    |
|                                               |
| Getestet auf Zen-Cart 1.3.5 & 1.3.6           |
=================================================

Die original Readme und Credits f�r die urspr�ngliche Contrib findet ihr in der Datei contrib_credits.txt


Diese Contrib stellt euch einen News Manager zur Verf�gung. 
Die News k�nnen im Admin Bereich eingegeben werden und werden in der Datenbank abgespeichert.

Angezeigt werden die News in einer Sidebox. 
Ihr k�nnt zwischen einer statischen Listenanzeige oder einem Java �berblendeffekt w�hlen.

Die Art der Anzeige und die Menge der anzuzeigenen News k�nnen konfiguriert werden.
Weiterhin ist ein Newsarchiv enthalten, wo alle bisher ver�ffentlichten News angezeigt werden.


Installation
=============
Es werden zwar keine Core Files �berschrieben, allerdings erfolgt einige Eintr�ge in die Datenbank.
Deshalb rate ich dringend zu einem Backup VOR der Installation!

Im Ordner includes/templates/ k�nnt ihr bei Bedarf den Ordner TEMPLATE_DEFAULT in euren Template Ordner umbenennen.

Ansonsten recht es, wenn ihr die Files einfach in der vorhandenen Struktur in eure Zen-Cart Installation einkopiert.

Danach geht ihr bitte in euer Adminpanel --> TOOLS --> SQL PATCHES INSTALLIEREN und f�hrt die Datei INSTALL.SQL aus. Sollte dieses nicht klappen, dann kopiert bitte den Inhalt der Datei INSTALL.SQL in das Fenster und geht auf SENDEN.

Nun m�sst ihr noch folgende Zeilen in eure stylesheet.css einf�gen:

.newsInfo {
  text-align: left;
  font-style: normal;
}

.newsContent {
font-size: 1.0em;
}

#newsArchivTitleHeading {
	text-align: left;
	}

#newsArchivDateHeading {
	text-align: right;
	}


Das wars schon mit der Installation.


Konfiguration
=============
Unter MEIN SHOP --> LAYOUTEINSTELLUNGEN findet ihr 3 neue Eintr�ge

News Box Character Count ist die Anzahl der Buchstaben einer News in der Sidebox. 
Sollte die News l�nger sein, wird danach der Text abgeschnitten (es entsteht ein sogenannter Appetizer) und auf Wunsch ein Link zur kompletten News angezeigt.  
Diese Einstellung ist nur aktiv wenn die News per Java �berblendeffekt angezeigt werden.

News Box Width & News Box Height in px sind die Gr��eneinstellungen der Sidebox.

In der Datei \includes\templates\template_default\templates\tpl_more_news_default.php k�nnen sowohl die Anzeigeart als auch die Menge der anzuzeigenden News eingestellt werden.


Die News selber verfasst ihr unter TOOLS --> NEWS BOX MANAGER


Bei Fragen oder Problemen bitte eine kurze eMail an mich oder postet unter www.zen-cart.at