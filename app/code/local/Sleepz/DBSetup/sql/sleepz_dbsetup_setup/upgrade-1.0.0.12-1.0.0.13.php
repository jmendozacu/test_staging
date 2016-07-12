<?php

/*
 * Adding the missing blocks gs_datenschutz, gs_business_terms, gs_revocation
 *
 */


#$configSwitch = new Mage_Core_Model_Config();

/*
 * SET SCOPE
 */
#$scope = 'websites';
$scopeId = 1;



/*
 * IF BLOG EXISTS, DELETE IT...
 */
$cmsBlock = Mage::getModel('cms/block')->load('gs_datenschutz', 'identifier');
if ($cmsBlock->getIdentifier()) {
    $cmsBlock->delete();
}
$cmsBlock = Mage::getModel('cms/block')->load('gs_business_terms', 'identifier');
if ($cmsBlock->getIdentifier()) {
    $cmsBlock->delete();
}
$cmsBlock = Mage::getModel('cms/block')->load('gs_revocation', 'identifier');
if ($cmsBlock->getIdentifier()) {
    $cmsBlock->delete();
}


/*
 * SET THE CONTENT...
 */
$blockcontent = <<<EOF
<h1>Datenschutzerklärung</h1>

<p>Datenschutz ist Vertrauenssache und Ihr Vertrauen ist uns wichtig. Wir respektieren Ihre Privat- und Persönlichkeitssphäre und tun alles, um Ihre Daten zu schützen. Diese Datenschutzerklärung informiert Sie darüber, wie wir Ihre personenbezogenen Daten verwenden. Sie geben uns mit dieser Erklärung Ihr Einverständnis dafür, dass perfekt-schlafen.de Ihre nachstehend aufgeführten personenbezogenen Daten zu den hier genannten Zwecken erheben, verarbeiten und nutzen darf. Diese Einwilligung kann jederzeit mit Wirkung für die Zukunft widerrufen werden. Wir können diese Datenschutzerklärung jederzeit durch Veröffentlichung der geänderten Bedingungen auf dieser Website ändern.</p>

<p>Diese Website nutzt ein Webanalyse-Tool. Es werden damit Interaktionen von zufällig ausgewählten, einzelnen Besuchern mit der Internetseite anonymisiert aufgezeichnet. So entsteht ein Protokoll von z.B. Mausbewegungen und -Klicks mit dem Ziel, Verbesserungsmöglichkeiten der jeweiligen Internetseite aufzuzeigen. Außerdem werden Informationen zum Betriebssystem, Browser, eingehende und ausgehende Verweise (Links), geografische Herkunft, sowie Auflösung und Art des Geräts zu statistischen Zwecken ausgewertet. <strong>Diese Informationen sind nicht personenbezogen und werden nicht an Dritte weitergegeben</strong>. Wenn Sie eine Aufzeichnung nicht wünschen, können Sie diese durch das Setzen des DoNotTrack-Headers in Ihrem Browser deaktivieren. Informationen dazu finden Sie auf der folgenden Seite: http://overheat.de/opt-out.html.“</p>

<br />

<a href="#pp_controller">Verantwortliche Stelle</a><br >
<a href="#pp_data">Welche Informationen über mich erhebt und benutzt perfekt-schlafen.de?</a><br />
<a href="#pp_share">Gibt perfekt-schlafen.de die erhaltenen Informationen weiter?</a><br />
<a href="#pp_plugins">Was sind Social Plugins und was hat es damit auf sich?</a><br />
<a href="#pp_analytics">Was ist Google Analytics und welche Informationen werden damit erhoben?</a><br />
<a href="#pp_cookies">Was sind Cookies und welche Vorteile habe ich davon?</a><br />
<a href="#pp_safety">Wie schützt perfekt-schlafen.de meine Daten?</a><br />
<a href="#pp_quit">Welche Rechte habe ich?</a><br />

<br />

<p><h5 id="pp_controller">Verantwortliche Stelle</h5>
Verantwortliche Stelle im Sinne der Datenschutzgesetze ist die sleepz GmbH, Seestraße 35, 14974 Ludwigsfelde, Deutschland, vertreten durch ihre Geschäftsführer Mark Beyer und Youssef Hassan. Diese erhebt und verarbeitet Ihre personenbezogenen Daten zur Erbringung ihrer Dienstleistungen <a href="/agb/">gemäß den Allgemeinen Geschäftsbedingungen</a> und gemäß den in dieser Datenschutzerklärung dargelegten Grundsätzen.
</p>

<br />

<p><h5 id="pp_data">Welche Informationen über mich erhebt und benutzt perfekt-schlafen.de?</h5>
Informationen, die wir von Ihnen erhalten, helfen uns, Ihr Einkaufserlebnis bei perfekt-schlafen.de zu verbessern und auf Ihre individuellen Bedürfnisse hin zu gestalten. Wir nutzen diese Informationen für die Abwicklung von Bestellungen, die Lieferung von Waren und das Erbringen von Dienstleistungen sowie die Abwicklung der Zahlung (bei Rechnungskauf auch für erforderliche Prüfungen). Wir verwenden Ihre Informationen auch, um mit Ihnen über Bestellungen, Produkte, Dienstleistungen und über Werbeangebote zu kommunizieren sowie dazu, unsere Datensätze zu aktualisieren und, sofern vorhanden, Ihr Kundenkonto bei uns zu unterhalten und zu pflegen sowie dazu, Inhalte wie z. B. Merkzettel oder Kundenbewertungen abzubilden und Ihnen Produkte oder Dienstleistungen zu empfehlen, die Sie interessieren könnten. Wir nutzen Ihre Informationen auch dazu, unsere Plattform zu verbessern, Missbrauch, insbesondere Betrug, vorzubeugen oder aufzudecken oder Dritten z.B. die Durchführung technischer, logistischer oder anderer Dienstleistungen in unserem Auftrag zu ermöglichen.</p>

<p>Sie können perfekt-schlafen.de besuchen, ohne dass wir wissen wer Sie sind oder wir personenbezogene Daten von Ihnen erheben. Sobald Sie uns Ihre personenbezogenen Daten mitteilen, sind Sie für uns nicht mehr anonym. Wenn Sie sich entscheiden, uns gegenüber personenbezogene Daten anzugeben, stimmen Sie der Übermittlung und Speicherung dieser Daten auf unseren Servern und auf den Servern von uns beauftragter Dritter zu. Mit Zustimmung zu dieser Datenschutzerklärung willigen Sie ein, dass wir folgende personenbezogenen Daten sammeln:</p>

<ul>
<p><li>E-Mail-Adresse, Kontaktdaten und (je nach genutzten Leistungen) manchmal Finanzinformationen wie Kreditkarten- oder Bankdaten;</li></p>

<p><li>Versand-, Rechnungs- und andere Informationen, die Sie für die Kauf- oder Versandabwicklung angeben;</li></p>

<p><li>Bewertungskommentare, sowie Beiträge in Blogs, Chats und Korrespondenz, die an uns geschickt wird;</li></p>

<p><li>andere Informationen aus Ihrer Interaktion mit unserer Website, unseren Diensten, Inhalten oder Werbeanzeigen, einschließlich Informationen über Ihren Computer bzw. Ihr Endgerät und über Ihre Internetverbindung, Statistiken zu Seitenaufrufen, Verkehrsdaten zu und von der Website, Werbedaten und übliche Webloginformationen;</li></p>

<p><li>zusätzliche Informationen, um die wir Sie bitten, um sich zu authentifizieren oder falls wir der Meinung sind, dass Sie unsere Grundsätze verletzen (beispielsweise können wir Sie bitten, uns eine Ausweiskopie oder die Kopie einer Rechnung zu senden, um Ihre Adresse zu bestätigen, oder zusätzliche Fragen online zu beantworten, um Ihre Identität zu prüfen);</li></p>

<p><li>Informationen von anderen Unternehmen, beispielsweise demografische Daten oder Navigationsdaten;</li></p>

<p><li>andere ergänzende Informationen von Dritten, wenn Sie beispielsweise Verbindlichkeiten bei perfekt-schlafen.de eingehen, überprüfen wir im gesetzlich zulässigen Rahmen generell Ihre Kreditwürdigkeit, indem wir über eine Wirtschaftsauskunftei weitere Informationen über Sie einholen. Sofern Ihre Angaben nicht verifiziert werden können, bitten wir Sie möglicherweise darum, uns zusätzliche Unterlagen, wie z.B. eine Kopie Ihres Personalausweises, einen Handelsregisterauszug bzw. andere Unterlagen zuzusenden, die Ihre Angaben bestätigen, oder online ergänzende Fragen zu beantworten, die uns bei der Verifizierung Ihrer Angaben helfen.</p>
</ul>

<br />

<p><h5 id="pp_share">Gibt perfekt-schlafen.de die erhaltenen Informationen weiter?</h5>

<p>Informationen über unsere Kunden sind wichtig und helfen uns, unser Angebot zu optimieren. Ihre personenbezogenen Daten werden von uns niemals zu Marketingzwecken an Dritte weitergegeben, verkauft oder vermietet. Wir geben die Informationen, die wir von Ihnen erhalten, ausschließlich in dem im Folgenden beschriebenen Umfang an Dritte weiter:</p>

<ul>
<p><li>Beauftragte andere Unternehmen und Einzelpersonen, die uns bei der Erbringung unserer Dienstleistungen und unseres Geschäftsbetriebes unterstützen. Beispiele sind u. a. Paketlieferung, Spedition, Sendung von Briefen oder E-Mails, die Pflege unserer Kundenlisten, die Analyse unserer Datenbanken, Werbemaßnahmen für perfekt-schlafen.de, Partner- und Prämienprogramme, Abwicklung von Zahlungen und Rechnungsinkasso (Kreditkarte, Lastschriftverfahren und Rechnungskauf) sowie Kundenservice. Dies beinhaltet auch einen Datenaustausch mit Unternehmen, die auf die Vorbeugung und Minimierung von Missbrauch und Kreditkartenbetrug spezialisiert sind. Diese Dienstleister haben Zugang zu persönlichen Informationen, die zur Erfüllung ihrer Aufgaben benötigt werden, sie dürfen diese jedoch nicht zu anderen Zwecken verwenden. Darüber hinaus sind sie verpflichtet, die Informationen gemäß dieser Datenschutzerklärung sowie den einschlägigen Datenschutzgesetzen zu behandeln.</li></p>

<p><li>Strafverfolgungs- oder andere Behörden oder Dritte aufgrund eines Auskunftsersuchens im Zusammenhang mit einem Ermittlungsverfahren oder wegen eines Verdachts einer Straftat, einer rechtswidrigen Handlung oder andere Handlungen, aus denen sich für uns, Sie oder einen anderen perfekt-schlafen-Nutzer eine rechtliche Haftung ergeben kann. Dabei können von uns folgende personenbezogene Daten offengelegt werden: Name, Straße, Postleitzahl, Ort, Bundesland, Land, Telefonnummer, E-Mail-Adresse, Bestellübersicht und alle anderen Informationen, die notwendig sind, um unmittelbaren physischen Schaden oder finanziellen Verlust zu verhindern oder eine vermutete rechtswidrige Handlung zu melden.</li></p>

<p><li>Andere Unternehmen, wenn wir eine Fusion mit oder eine Übernahme durch dieses Unternehmen planen. Sollte ein solcher Zusammenschluss eintreten, werden wir von dem neuen zusammengeschlossenen Unternehmen verlangen, dass es diese Datenschutzerklärung in Bezug auf Ihre personenbezogenen Daten befolgt. Sollten Ihre personenbezogenen Daten entgegen dieser Datenschutzerklärung verwendet werden, werden Sie im Voraus darüber informiert.
</ul>

<br />

<p><h5 id="pp_plugins">Was sind Social Plugins und was hat es damit auf sich?</h5>

<p>Unser Internetauftritt verwendet Social Plugins (“Plugins”) des sozialen Netzwerks facebook.com. Diese Plugins werden von der Facebook Inc., 1601 Willow Road, Menlo Park, CA 94205, USA (im folgenden “Facebook” oder "Dienst" genannt) betrieben. Die Plugins sind jeweils am jeweiligen Facebook Logo erkennbar. Die Liste und das Aussehen der Social Plugins kann für Facebook auf <a href="http://developers.facebook.com/plugins">www.developers.facebook.com/plugins</a> eingesehen werden.</p>

<p>Wenn Sie eine Webseite unseres Internetauftritts aufrufen, die ein solches Plugin enthält, baut Ihr Browser eine direkte Verbindung mit den Servern von Facebook auf. Der Inhalt des Plugins wird von Facebook direkt an Ihren Browser übermittelt und von diesem in die Webseite eingebunden. Wir haben daher keinen Einfluss auf den Umfang der Daten, die Facebook mit Hilfe dieser Plugins erhebt und informieren Sie daher entsprechend unserem Kenntnisstand:</p>

<p>Durch die Einbindung der Plugins erhält Facebook die Information, dass Sie die entsprechende Seite unseres Internetauftritts aufgerufen haben. Sind Sie bei Facebook eingeloggt, können diese den Besuch Ihrem jeweiligen Facebook-Konto zuordnen. Wenn Sie mit den Plugins interagieren, zum Beispiel den Like-Button betätigen oder einen Kommentar abgeben, wird die entsprechende Information von Ihrem Browser direkt an den jeweiligen Dienst übermittelt und dort gespeichert. Falls Sie kein Mitglied von Facebook sind, besteht trotzdem die Möglichkeit, dass Facebook Ihre IP-Adresse in Erfahrung bringt und speichert.</p>

<p>Zweck und Umfang der Datenerhebung und die weitere Verarbeitung und Nutzung der Daten durch Facebook sowie Ihre diesbezüglichen Rechte und Einstellungsmöglichkeiten zum Schutz Ihrer Privatssphäre entnehmen Sie bitte den Datenschutzhinweisen des jeweiligen Diensteanbieters. Für Facebook finden Sie diese auf <a href="http://www.facebook.com/about/privacy/">www.facebook.com/about/privacy</a>.</p>

<p>Wenn Sie Facebook-Mitglied sind und nicht möchten, dass Facebook über unseren Internetauftritt Daten über Sie sammelt und mit Ihren bei Facebook gespeicherten Mitgliedsdaten verknüpft, müssen Sie sich vor Ihrem Besuch unseres Internetauftritts bei Facebook ausloggen. Ebenfalls ist es möglich Facebook-Social-Plugins mit Add-ons für Ihren Browser zu blocken, zum Beispiel mit dem <a href="http://webgraph.com/resources/facebookblocker/">Facebook Blocker</a>.</p>

<br />

<p><h5 id="pp_analytics">Was ist Google Analytics und welche Informationen werden damit erhoben?</h5>

<p>Diese Website benutzt Google Analytics, einen Webanalysedienst der Google Inc. („Google“). Google Analytics verwendet sog. „Cookies“, Textdateien, die auf Ihrem Computer gespeichert werden und die eine Analyse der Benutzung der Website durch Sie ermöglichen. Die durch den Cookie erzeugten Informationen über Ihre Benutzung dieser Website werden in der Regel an einen Server von Google in den USA übertragen und dort gespeichert.</p>

<p>Im Falle der Aktivierung der IP-Anonymisierung auf dieser Webseite, wird Ihre IP-Adresse von Google jedoch innerhalb von Mitgliedstaaten der Europäischen Union oder in anderen Vertragsstaaten des Abkommens über den Europäischen Wirtschaftsraum zuvor gekürzt. Nur in Ausnahmefällen wird die volle IP-Adresse an einen Server von Google in den USA übertragen und dort gekürzt. Die IP-Anonymisierung ist auf dieser Website aktiv. Im Auftrag des Betreibers dieser Website wird Google diese Informationen benutzen, um Ihre Nutzung der Website auszuwerten, um Reports über die Websiteaktivitäten zusammenzustellen und um weitere mit der Websitenutzung und der Internetnutzung verbundene Dienstleistungen gegenüber dem Websitebetreiber zu erbringen.</p>

<p>Die im Rahmen von Google Analytics von Ihrem Browser übermittelte IP-Adresse wird nicht mit anderen Daten von Google zusammengeführt. Sie können die Speicherung der Cookies durch eine entsprechende Einstellung Ihrer Browser-Software verhindern; wir weisen Sie jedoch darauf hin, dass Sie in diesem Fall gegebenenfalls nicht sämtliche Funktionen dieser Website vollumfänglich werden nutzen können. Sie können darüber hinaus die Erfassung der durch das Cookie erzeugten und auf Ihre Nutzung der Website bezogenen Daten (inkl. Ihrer IP-Adresse) an Google sowie die Verarbeitung dieser Daten durch Google verhindern, indem sie das unter dem folgenden Link <a href="http://tools.google.com/dlpage/gaoptout?hl=de">Browser-Plugin herunterladen und installieren</a>.</p>

<br />

<p><h5 id="pp_cookies">Was sind Cookies und welche Vorteile habe ich davon?</h5>

<p>Cookies sind kleine Dateien, die auf der Festplatte Ihres Computers gespeichert werden und helfen, Ihren Einkauf auf der perfekt-schlafen.de einfacher, sicherer und bequemer zu machen. Aus Datenschutzgründen setzen wir diese Hilfsmittel jedoch nur in begrenztem Umfang ein. Bei den meisten dieser Cookies handelt es sich um „sitzungsbezogene Cookies“, die automatisch von der Festplatte gelöscht werden, wenn Sie die Sitzung beenden, d.h. sich ausloggen oder den Browser schließen. Sofern Ihr Browser das zulässt, können Sie Cookies jederzeit deaktivieren. Beachten Sie bitte, dass Sie dann bestimmte Funktionen auf perfekt-schlafen.de möglicherweise nicht mehr nutzen können.</p>


<p>Wir setzen Cookies auf bestimmten Seiten aus folgenden Gründen ein:</p>

<ul>
<p><li>Um Ihren Einkauf einfacher und bequemer zu machen, z.B. indem Sie Ihr Passwort – wenn Sie über ein Kundenkonto verfügen – während einer Sitzung seltener eingeben müssen.</li></p>

<p><li>Um Ihre Sicherheit zu erhöhen.</li></p>

<p><li>Um Ihnen Angebote und Informationen bereitzustellen, die Sie interessieren könnten.</li></p>

<p><li>Um die Werbewirksamkeit unserer Angebots einzuschätzen und anzupassen.</li></p>

<p><li>Um das Nutzungsverhalten auf unserer Website zu analysieren, damit wir perfekt-schlafen.de noch besser für Sie machen können.</li></p>
</ul>

<br />

<p><h5 id="pp_safety">Wie schützt perfekt-schlafen.de meine Daten?</h5>

<p>Wir tun alles, um Ihre Daten zu schützen und passen unsere Sicherheitsmaßnahmen regelmäßig der technischen Entwicklung an. Zu Ihrer Sicherheit verschlüsseln wir Ihre Daten mit dem SSL-Verfahren (Secure-Socket-Layer) und haben weitere technische und organisatorische Vorkehrungen getroffen, die Ihre personenbezogenen Daten vor Verlust, Manipulation und unerlaubtem Zugriff Dritter im Rahmen unserer Möglichkeiten schützen.</p>

<br />

<p><h5 id="pp_quit">Welche Rechte habe ich?</h5>

<p>Sie haben ein Recht auf Auskunft über die zu Ihrer Person oder zu Ihrem Pseudonym gespeicherten Daten und ggf. ein Recht auf Berichtigung, Löschung oder Sperrung dieser Daten. Die Auskunft kann Ihnen auf Verlangen auch elektronisch erteilt werden.</p>

<p>Wenn Sie über ein Kundenkonto bei perfekt-schlafen.de verfügen, können Sie eine Vielzahl von Informationen über Ihr Konto und Ihre Interaktion mit perfekt-schlafen.de einsehen und in bestimmten Fällen aktualisieren. Dazu gehören Informationen über frühere Bestellungen, persönliche Daten (einschließlich Name, E-Mail-, Rechnungs- und Versandadressen, Passwort), Einstellungen zu Zahlungsarten, Angaben darüber, welche Informationen Sie per E-Mail von uns erhalten möchten (einschließlich perfekt-schlafen.de-Newsletter) sowie Ihre Bewertungen und Merklisten.</p>

<p>Sie können Ihre Einwilligungserklärung jederzeit mit Wirkung für die Zukunft widerrufen. Wenn Sie Fragen in Bezug auf diese Datenschutzerklärung haben, Auskunft über die zu Ihrer Person oder zu Ihrem Pseudonym gespeicherten Daten erhalten oder Ihre Einwilligungserklärung widerrufen möchten, können Sie <a href="/kontakt/">eine Nachricht an den perfekt-schlafen-Kundenservice schicken</a> oder wenden Sie sich auf dem Postweg an:</p>

<p>sleepz GmbH<br />
perfekt-schlafen.de<br />
Seestraße 35<br />
14979 Ludwigsfelde<br />
Deutschland</p>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Legal Datenschutz');
$cmsBlock->setIdentifier('gs_datenschutz');
$cmsBlock->setContent($blockcontent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


$blockcontent = <<<EOF
<h1>Allgemeine Gesch&auml;ftsbedingungen (AGB)</h1>
<p>Stand: Oktober 2015 | Version: 1.3</p>
<p>&nbsp;</p>
<h5>1. Allgemeine Bestimmungen / Geltung</h5>
<p>1.1 Die sleepz GmbH (im Folgenden: &bdquo;sleepz&ldquo; oder &bdquo;Verk&auml;ufer&ldquo;) bietet Kunden &uuml;ber den Online-Shop unter der Domain http://www.perfekt-schlafen.de als gewerbliche Anbieterin Waren zum Kauf an. Nachstehende allgemeine Gesch&auml;ftsbedingungen (AGB) gelten f&uuml;r die &uuml;ber diesen Online-Shop zwischen sleepz und dem Kunden geschlossenen Vertr&auml;ge.</p>
<p>1.2 Kunden im Sinne der vorliegenden AGB sind Verbraucher wie auch Unternehmer.</p>
<p>1.2.1 Verbraucher ist jede nat&uuml;rliche Person, mit der in Gesch&auml;ftsbeziehung getreten wird und die zu einem Zweck handelt, der weder ihrer gewerblichen noch ihrer selbst&auml;ndigen beruflichen T&auml;tigkeit zugerechnet werden kann.</p>
<p>1.2.2 Unternehmer ist jede nat&uuml;rliche oder juristische Person oder rechtsf&auml;hige Personengesellschaft, mit der in Gesch&auml;ftsbeziehung getreten wird und die in Aus&uuml;bung einer gewerblichen oder selbst&auml;ndigen beruflichen T&auml;tigkeit handelt.</p>
<p>1.3 Die Lieferung der Waren erfolgt an eine g&uuml;ltige Versandadresse innerhalb Deutschlands.</p>
<p>1.4 Vertragssprache ist Deutsch.</p>
<p>&nbsp;</p>
<h5>2. Vertragsabschluss im Online-Shop / Vertragstextspeicherung</h5>
<p>2.1 S&auml;mtliche Angebote sind freibleibend. Insbesondere die in den Produktbeschreibungen enthaltenen Gewichts- und Ma&szlig;angaben, Zeichnungen, Erl&auml;uterungen, Beschreibungen und Abbildungen sind branchen&uuml;bliche N&auml;herungswerte. Mit der Pr&auml;sentation der Waren innerhalb des Online-Shops und der Einr&auml;umung der M&ouml;glichkeit zur Bestellung ist kein verbindliches Angebot seitens sleepz verbunden. Erst die Bestellung des Kunden stellt ein verbindliches Angebot an sleepz zum Abschluss eines Kaufvertrages &uuml;ber die in dem virtuellen Warenkorb enthaltenen Artikel dar. Wenn Kunden eine Bestellung bei sleepz aufgeben, sendet sleepz diesen an die von ihnen angegebene E-Mail-Adresse eine E-Mail zu, mit der der Eingang der Bestellung best&auml;tigt und ggf. deren Einzelheiten aufgef&uuml;hrt werden (Bestellbest&auml;tigung). Diese Bestellbest&auml;tigung stellt nur dann die Annahme des Angebotes durch sleepz dar, wenn sie mit einer Zahlungsaufforderung verbunden ist. Anderenfalls soll die Bestellbest&auml;tigung den Kunden nur dar&uuml;ber informieren, dass seine Bestellung bei sleepz eingegangen ist. Ein Kaufvertrag mit sleepz kommt erst dann zustande, wenn sleepz das Angebot des Kunden annimmt. Die Annahme des Angebots des Kunden durch sleepz kann nur innerhalb von zwei Tagen nach dem Eingang der Bestellung entweder durch Versendung einer Auftragsbest&auml;tigung von sleepz an den Kunden oder durch Versendung der bestellten Ware zur Auslieferung an den Kunden. Der Kunde verzichtet nach &sect; 151 Satz 1 BGB auf den Zugang einer Annahmeerkl&auml;rung.</p>
<p>2.2 Der Kunde hat die M&ouml;glichkeit, innerhalb des Online-Shops Produkte auszuw&auml;hlen und zu bestellen. Vor Absendung der Bestellung erm&ouml;glicht sleepz dem Kunden, die Bestelldaten zu &uuml;berpr&uuml;fen und etwaige Eingabefehler zu berichtigen.</p>
<p>2.3 F&uuml;r Kunden mit einem Kundenaccount wird der Vertragstext abrufbar gespeichert; er ist &uuml;ber den internen Kundenbereich nach Absendung der Bestellung abrufbar. Der Login erfolgt direkt &uuml;ber die Website von sleepz unter Angabe der E-Mail-Adresse sowie des zuvor vom Kunden selbst bestimmten Passwortes.</p>
<p>2.4 Der Verk&auml;ufer &uuml;bernimmt kein Beschaffungsrisiko. Der Abschluss des Kaufvertrages erfolgt daher unter dem Vorbehalt, im Falle nicht richtiger oder nicht ordnungsgem&auml;&szlig;er Selbstbelieferung durch Zulieferer nicht oder nur teilweise zu leisten und ggf. von dem Vertrag zur&uuml;ckzutreten. Die Verantwortlichkeit des Verk&auml;ufers f&uuml;r Vorsatz oder Fahrl&auml;ssigkeit bleibt unber&uuml;hrt. Im Fall der Nichtverf&uuml;gbarkeit oder der nur teilweisen Verf&uuml;gbarkeit der Leistung wird der Verk&auml;ufer den Kunden unverz&uuml;glich informieren; die Gegenleistung wird im Fall des R&uuml;cktritts unverz&uuml;glich an den Kunden zur&uuml;ckerstattet.</p>
<p>&nbsp;</p>
<h5>3. Preise / Zahlungsbedingungen</h5>
<p>3.1 Alle Preise stellen Endpreise dar &ndash; d.h. sie beinhalten s&auml;mtliche Preisbestandteile einschlie&szlig;lich anfallender Umsatzsteuer. Es gilt der Betrag, der jeweils zum Zeitpunkt der verbindlichen Bestellung ausgewiesen ist. Hinzu kommen Liefer- und Versandkosten, die von der Versandart, der Gr&ouml;&szlig;e und dem Gewicht der vom Kunden bestellten Ware(n) abh&auml;ngig sind und dem Kunden gesondert in Rechnung gestellt werden k&ouml;nnen, wenn und soweit Liefer- und Versandkosten Bestandteil des Angebots sind; ist dies nicht der Fall, erfogt die Lieferung liefer- und versandtkostenfrei. N&auml;here Informationen finden sich unter Versand und Lieferung. Die regelm&auml;&szlig;igen Kosten der R&uuml;cksendung, die im Falle einer R&uuml;ckgabe der Ware durch den K&auml;ufer in Aus&uuml;bung seines Widerrufsrechts entstehen, tr&auml;gt sleepz. Bei Aus&uuml;bung des Widerrufsrechts durch den Kunden werden die Versandkosten r&uuml;ckerstattet.</p>
<p>3.2 sleepz akzeptiert die innerhalb des Online-Shops angef&uuml;hrten und dem Kunden zur Auswahl gestellten Zahlungsmethoden. Der Kunde w&auml;hlt die von ihm bevorzugte Zahlungsart unter den zur Verf&uuml;gung stehenden Zahlungsmethoden selbst aus.</p>
<p>3.2.1 Im Fall einer Zahlung durch Bank&uuml;berweisung oder PayPal verpflichtet sich der Kunde, den Kaufpreis zzgl. anfallender Liefer- und Versandkosten, wenn und soweit solche laut Angebot vom Kunden zu tragen sind, sp&auml;testens 14 Tage nach Erhalt der ihm mitgeteilten Zahlungsaufforderung ohne Abzug zu zahlen. Die Konto- bzw. &Uuml;berweisungsdaten werden zusammen mit der Zahlungsaufforderung mitgeteilt.</p>
<p>3.3 Der Kunde kann ein Zur&uuml;ckbehaltungsrecht nur aus&uuml;ben, soweit sein Gegenanspruch auf demselben Vertragsverh&auml;ltnis beruht. Ein Recht zur Aufrechnung steht dem Kunden nur zu, wenn seine Gegenanspr&uuml;che rechtskr&auml;ftig festgestellt oder von sleepz unbestritten sind.</p>
<p>3.4 Verbraucher tragen nicht die Gefahr des zuf&auml;lligen Untergangs und der zuf&auml;lligen Verschlechterung der verkauften Sache auf dem Transportwege (vgl. im Einzelnen die nachfolgende Ziffer 4.3). Der Kunde ist dennoch berechtigt, eine (Transport-) Versicherung zu verlangen oder eine solche kann aufgrund weiterer Vertragsbedingungen vorgesehen sein. Verlangt der Kunde ausdr&uuml;cklich eine (Transport-) Versicherung oder ist eine solche aufgrund der weiteren Vertragsbedingungen vorgesehen, ist sleepz berechtigt, die dadurch bedingten Mehrkosten dem Kunden gesondert in Rechnung zu stellen.</p>
<p>3.5 Kommt der Kunde in Zahlungsverzug, so ist sleepz berechtigt, Verzugszinsen in H&ouml;he von 5 Prozentpunkten &uuml;ber dem von der Deutschen Bundesbank f&uuml;r den Zeitpunkt der Bestellung bekannt gegebenen Basiszinssatz p.a. bei Verbrauchern und 8 Prozentpunkten &uuml;ber dem von der Deutschen Bundesbank f&uuml;r den Zeitpunkt der Bestellung bekannt gegebenen Basiszinssatz p.a. bei Unternehmern zu fordern. Falls sleepz ein h&ouml;herer Verzugsschaden nachweisbar entstanden ist, ist sleepz berechtigt, diesen geltend zu machen.</p>
<p>3.6<strong> Zus&auml;tzliche Allgemeine Gesch&auml;ftsbestimmungen und Datenschutzhinweis der RatePAY GmbH</strong><br />Um Ihnen attraktive Zahlungsarten anbieten zu k&ouml;nnen, arbeiten wir mit der&nbsp;<strong>RatePAY GmbH, Schl&uuml;terstra&szlig;e 39, 10629 Berlin&nbsp;</strong>(nachfolgend "RatePAY") zusammen. Kommt bei Nutzung einer RatePAY-Zahlungsart ein wirksamer Kaufvertrag zwischen Ihnen und uns zustande,&nbsp;<strong>treten wir&nbsp;</strong>unsere&nbsp;<strong>Zahlungsforderung&nbsp;</strong>an RatePAY&nbsp;<strong>ab</strong>. Bei Nutzung der RatePAY-Zahlungsart Ratenzahlung, treten wir unsere Zahlungsforderung an die Partnerbank der RatePAY GmbH ab.<br />Wenn Sie eine der hier angebotenen RatePAY-Zahlungsarten w&auml;hlen, willigen Sie im Rahmen Ihrer Bestellung in die&nbsp;<strong>Weitergabe&nbsp;</strong>Ihrer pers&ouml;nlichen&nbsp;<strong>Daten&nbsp;</strong>und die der Bestellung, zum Zwecke der&nbsp;<strong>Identit&auml;ts-&nbsp;</strong>und&nbsp;<strong>Bonit&auml;tspr&uuml;fung</strong>, sowie der&nbsp;<strong>Vertragsabwicklung,&nbsp;</strong>an die RatePAY GmbH ein. Alle Einzelheiten finden Sie in den <a href="http://www.ratepay.com/zusaetzliche-geschaeftsbedingungen-und-datenschutzhinweis">zus&auml;tzlichen Allgemeinen Gesch&auml;ftsbedingungen und dem Datenschutzhinweis f&uuml;r RatePAY-Zahlungsarten</a>, die Teil dieser Allgemeinen Gesch&auml;ftsbedingungen sind und immer dann Anwendungen finden, wenn Sie sich f&uuml;r eine RatePAY-Zahlungsart entscheiden.</p>
<p>&nbsp;</p>
<h5>4. Liefer- und Versandbedingungen</h5>
<p>4.1 Die Lieferung der Ware erfolgt, sofern nicht ausdr&uuml;cklich eine Selbstabholung durch den Kunden vereinbart ist, auf dem Versandwege an die vom Kunden mitgeteilte Lieferanschrift.</p>
<p>4.2 Der Versand der vom Kunden gekauften Artikel erfolgt innerhalb der im Artikel hinterlegten Lieferzeit nach Zahlungseingang. sleepz ist berechtigt, die Ware auch schon vor Zahlungseingang an den Kunden zu versenden. Der Versand per Spedition wird nach deutschem Speditionsrecht abgehandelt. Verlangt der Kunde eine Verbringung der Ware bis zur ersten abschlie&szlig;baren T&uuml;r oder dar&uuml;ber hinaus, ist der Verk&auml;ufer berechtigt, die dadurch bedingten Mehrkosten dem Kunden gesondert in Rechnung zu stellen.</p>
<p>4.3 Bei Unternehmern geht die Gefahr des zuf&auml;lligen Untergangs und der zuf&auml;lligen Verschlechterung der verkauften Sache mit der &Uuml;bergabe an diese selbst oder eine empfangsberechtigte Person, beim Versendungskauf mit der Auslieferung der Ware an eine geeignete Transportperson &uuml;ber. Bei Verbrauchern geht die Gefahr des zuf&auml;lligen Untergangs und der zuf&auml;lligen Verschlechterung der verkauften Sache stets mit der &Uuml;bergabe der Ware auf den Verbraucher &uuml;ber. Der &Uuml;bergabe steht es gleich, wenn der Kunde in den Verzug der Annahme ger&auml;t.</p>
<p>4.4 Macht der Kunde unzutreffende oder l&uuml;ckenhafte Angaben zu seiner Anschrift oder kann aus anderen Gr&uuml;nden, die der Kunde zu vertreten hat, die Ware nicht abgeliefert werden, kommt der Kunde durch den erfolglosen Anlieferungsversuch des Transportunternehmens in Annahmeverzug. Ein Annahmeverzug des Kunden aus anderen Gr&uuml;nden bleibt unber&uuml;hrt. Annahmeverzug hat zur Folge, dass der Kunde f&uuml;r den Fall, dass die Kaufsache aus Gr&uuml;nden, die der Verk&auml;ufer nicht vors&auml;tzlich oder grob fahrl&auml;ssig zu vertreten hat, besch&auml;digt wird oder untergeht, zur Zahlung des Kaufpreises verpflichtet bleibt, w&auml;hrend der Verk&auml;ufer die Leistung nicht mehr bewirken muss. Des Weiteren hat der Kunde dem Verk&auml;ufer die Kosten, die durch den Annahmeverzug sowie f&uuml;r die Erhaltung und Aufbewahrung der Kaufsache entstanden sind, zu erstatten. Wird der Versand auf Wunsch oder aus Verschulden des Kunden um mehr als zwei Wochen nach der Anzeige der Versandbereitschaft des Verk&auml;ufers verz&ouml;gert, kann der Verk&auml;ufer pauschal f&uuml;r jeden Monat ein Lagergeld in H&ouml;he von 0,3 % des Preises des Liefergegenstandes berechnen. Dem Kunden ist der Nachweis gestattet, dass dem Verk&auml;ufer kein Schaden oder ein wesentlich niedrigerer Schaden entstanden ist; dem Verk&auml;ufer ist der Nachweis gestattet, dass ein h&ouml;herer Schaden entstanden ist. Weitergehende und sonstige Rechte des Verk&auml;ufers bleiben unber&uuml;hrt.</p>
<p>4.5 Macht h&ouml;here Gewalt (z.B. Naturkatastrophen jeder Art, insbesondere Unwetter, &Uuml;berschwemmungen, Krieg, Terrorismus , Streiks oder Sabotage) die Lieferung oder eine sonstige Leistung dauerhaft unm&ouml;glich, ist eine Leistungspflicht sleepzs ausgeschlossen. Bereits gezahlte Betr&auml;ge werden von sleepz unverz&uuml;glich erstattet.</p>
<p>4.6 sleepz kann au&szlig;erdem die Leistung verweigern und von dem Kaufvertrag zur&uuml;cktreten, soweit es zu einem Wegfall der Gesch&auml;ftsgrundlage kommt, was z.B. der Fall ist, wenn die Leistung einen Aufwand erfordert, der unter Beachtung des Inhalts des Kaufvertrages und der Gebote von Treu und Glauben in einem groben Missverh&auml;ltnis zu dem Interesse des Kunden an der Erf&uuml;llung des Kaufvertrages steht. Bereits gezahlte Betr&auml;ge werden von sleepz unverz&uuml;glich erstattet.</p>
<p>&nbsp;</p>
<h5>5.</h5>
<p>{{block type="cms/block" block_id="gs_revocation"}} </p>
<h5 id="probeschlafen">6. Probeschlafen</h5>
<p>6.1. sleepz bietet dem Kunden die M&ouml;glichkeit des 30t&auml;gigen Probeschlafens f&uuml;r Artikel der Kategorien Matratzen und Lattenroste. Der Kunde ist berechtigt, gelieferte Waren der vorgenannten Kategorien im Rahmen einer bestimmungsgem&auml;&szlig;en und &uuml;blichen Nutzung f&uuml;r einen Zeitraum von 30 Tagen ab Lieferung zu testen. Der 30t&auml;gige Testzeitraum endet vorzeitig, wenn der Kunde seine Vertragserkl&auml;rung in Aus&uuml;bung seines Widerrufsrechts widerruft; in diesem Fall endet der Testzeitraum mit der Absendung der Widerrufserkl&auml;rung durch den Kunden. Ist der Kunde nicht zufrieden, nimmt sleepz aufgrund eines entsprechenden Verlangens des Kunden die betroffene Ware endg&uuml;ltig zur&uuml;ck, wenn der Kunde von sleepz die R&uuml;cknahme bis zum Ablauf des letzten Tages der 30t&auml;gigen Probephase in Textform (z.B. per E-Mail) verlangt und wenn sleepz nicht nach Ziffer 6.2 berechtigt ist, die R&uuml;cknahme zu verweigern. Der R&uuml;cktransport wird durch sleepz auf eigene Kosten organisiert.</p>
<p>6.2 Nach Eingang der Ware bei sleepz wird diese &uuml;berpr&uuml;ft. sleepz erstattet dem Kunden den gezahlten Kaufpreis, wenn diese &Uuml;berpr&uuml;fung ergibt, dass die Ware nur bestimmungsgem&auml;&szlig; und in &uuml;blicher Art und Weise genutzt wurde. Weist die Ware allerdings ungew&ouml;hnlich starke Gebrauchsspuren oder Beeintr&auml;chtigungen auf, z.B. Verschmutzungen, Risse, Kratzer, die durch eine unsachgem&auml;&szlig;e Behandlung oder eine &uuml;berm&auml;&szlig;ige Beanspruchung durch den Kunden verursacht worden sein k&ouml;nnen, ist sleepz berechtigt, den Betrag des daraus entstandenen Wertverlusts vom Kaufpreis der jeweiligen Ware einzubehalten und nur den Restbetrag an den Kunden zu erstatten oder die R&uuml;cknahme ganz zu verweigern und keine Erstattung zu leisten. sleepz wird den Kunden in diesen F&auml;llen &uuml;ber die beabsichtigte Vorgehensweise unter Verwendung der vom Kunden angegebenen E-Mail-Adresse unterrichten. Ist der Kunde mit der angek&uuml;ndigten Vorgehensweise nicht einverstanden, kann er auf eigene Kosten von sleepz die R&uuml;cksendung der Ware an sich verlangen; eine &ndash; auch teilweise &ndash; Erstattung des Kaufpreises ist in diesem Fall endg&uuml;ltig ausgeschlossen.</p>
<p>6.3. Das Widerrufsrecht des Verbrauchers nach Ziffer 5. bleibt von den vorstehenden Regelungen unber&uuml;hrt.</p>
<p>&nbsp;</p>
<h5>7. Eigentumsvorbehalt</h5>
<p>Bis zur vollst&auml;ndigen Begleichung aller gegen den Kunden bestehender Anspr&uuml;che aus dem Kaufvertrag bleibt die gelieferte Ware im Eigentum von sleepz. Solange dieser Eigentumsvorbehalt besteht, darf der Kunde die Ware weder weiterver&auml;u&szlig;ern noch &uuml;ber die Ware verf&uuml;gen; insbesondere darf der Kunde Dritten vertraglich keine Nutzung an der Ware einr&auml;umen.</p>
<p>&nbsp;</p>
<h5>8. Rechte bei M&auml;ngeln der Sache (Gew&auml;hrleistung)</h5>
<p>8.1 Anfragen und/oder Beanstandungen jeglicher Art sind an sleepz &uuml;ber die unten angef&uuml;hrten Kontaktdaten zu richten.</p>
<p>8.2 Ein bereits bei der Lieferung mangelhaftes Produkt (Gew&auml;hrleistungsfall) wird sleepz nach Wahl des Kunden &ndash; bei Unternehmern als Kunden nach Wahl sleepzs &ndash; auf Kosten von sleepz durch ein mangelfreies ersetzen oder fachgerecht reparieren lassen (Nacherf&uuml;llung). Der Kunde wird darauf hingewiesen dass kein Gew&auml;hrleistungsfall vorliegt, wenn das Produkt bei Gefahr&uuml;bergang die vereinbarte Beschaffenheit hatte. Ein Gew&auml;hrleistungsfall liegt insbesondere in folgenden F&auml;llen nicht vor:</p>
<p>a) bei Sch&auml;den, die beim Kunden durch Missbrauch, unsachgem&auml;&szlig;en Gebrauch oder unsachgem&auml;&szlig;e Behandlung, insbesondere, jedoch ohne Beschr&auml;nkung hierauf, durch Verwendung einer mangelhaften Unterlage oder durch &uuml;berdurchschnittlichen Gebrauch des Artikels, entstanden sind;</p>
<p>b) bei Sch&auml;den, die dadurch entstanden sind, dass die Produkte beim Kunden sch&auml;dlichen &auml;u&szlig;eren Einfl&uuml;ssen ausgesetzt worden sind (insbesondere, jedoch ohne Beschr&auml;nkung hierauf, extremen Temperaturen, Feuchtigkeit, au&szlig;ergew&ouml;hnlicher physikalischer, chemischer oder elektrischer Beanspruchung, Spannungsschwankungen, Blitzschlag, statischer Elektrizit&auml;t, Feuer).</p>
<p>8.3 sleepz leistet ferner keine Gew&auml;hr f&uuml;r einen Fehler, der durch unsachgem&auml;&szlig;e Reparatur durch einen nicht vom Hersteller autorisierten Servicepartner entstanden ist.</p>
<p>8.4 Erfordert die vom Kunden gew&uuml;nschte Art der Nacherf&uuml;llung (Ersatzlieferung oder Reparatur) einen Aufwand, der in Anbetracht des Produktpreises unter Beachtung des Vertragsinhaltes und der Gebote von Treu und Glauben in einem groben Missverh&auml;ltnis zu dem Leistungsinteresse des Kunden steht &ndash; wobei insbesondere der Wert des Kaufgegenstandes im mangelfreien Zustand, die Bedeutung des Mangels und die Frage zu ber&uuml;cksichtigen sind, ob auf die andere Art der Nacherf&uuml;llung ohne erhebliche Nachteile f&uuml;r den Kunden zur&uuml;ckgegriffen werden kann &ndash;, beschr&auml;nkt sich der Anspruch des Kunden auf die jeweils andere Art der Nacherf&uuml;llung. Das Recht von sleepz, auch diese andere Art der Nacherf&uuml;llung unter der vorgenannten Voraussetzung zu verweigern, bleibt unber&uuml;hrt.</p>
<p>8.5 Sowohl f&uuml;r den Fall der Reparatur als auch f&uuml;r den Fall der Ersatzlieferung ist der Kunde verpflichtet, das Produkt auf Kosten von sleepz unter Angabe der Bestellnummer an die von sleepz angegebene R&uuml;cksendeadresse einzusenden. Vor der Einsendung hat der Kunde von ihm eingef&uuml;gte oder angebrachte Gegenst&auml;nde aus oder von dem Produkt zu entfernen. sleepz ist nicht verpflichtet, das Produkt auf das Vorhandensein solcher Gegenst&auml;nde zu untersuchen. F&uuml;r den Verlust solcher Gegenst&auml;nde haftet sleepz nicht, es sei denn, es war bei R&uuml;cknahme des Produktes f&uuml;r sleepz ohne Weiteres erkennbar, dass ein solcher Gegenstand in oder an dem Produkt vorhanden war (in diesem Fall informiert sleepz den Kunden und h&auml;lt den Gegenstand f&uuml;r den Kunden zur Abholung bereit; der Kunde tr&auml;gt die dabei entstehenden Kosten).</p>
<p>8.6 Offensichtliche M&auml;ngel sind durch Unternehmer innerhalb einer Frist von 2 Wochen ab Empfang der Ware &uuml;ber obig aufgef&uuml;hrte Kontaktdaten anzuzeigen; ansonsten ist die Geltendmachung des Gew&auml;hrleistungsanspruchs ausgeschlossen. Zur Wahrung der Frist gen&uuml;gt die rechtzeitige Absendung der Mangelanzeige.</p>
<p>8.7 Im Fall von Transportsch&auml;den verpflichtet sich der Kunde, diese unverz&uuml;glich an den Verk&auml;ufer mitzuteilen und diesen bei der Geltendmachung von Anspr&uuml;chen gegen&uuml;ber dem jeweiligen Transportunternehmen bzw. der Transportversicherung nach besten Kr&auml;ften zu unterst&uuml;tzen. Bei offensichtlichen Sch&auml;den verpflichtet sich der Kunde die Annahme der Ware zu verweigern. Will der Kunde die Annahme wegen der Geringf&uuml;gigkeit des Schadens nicht verweigern, ist er verpflichtet, dies schriftlich festzuhalten, sich durch den Auslieferer quittieren zu lassen und eine Kopie des quittierten Schriftst&uuml;cks unverz&uuml;glich an sleepz zu &uuml;bersenden, wobei eine &Uuml;bersendung per E-Mail gen&uuml;gt.</p>
<p>8.8 Sendet der Kunde die Ware ein, um ein Austauschprodukt zu bekommen, richtet sich die R&uuml;ckgew&auml;hr des mangelhaften Produktes nach folgender Ma&szlig;gabe: Sofern der Kunde die Ware zwischen Lieferung und R&uuml;cksendung in mangelfreiem Zustand benutzen konnte, hat dieser den Wert der von ihm gezogenen Nutzungen zu erstatten. F&uuml;r einen nicht durch den Mangel eingetretenen Untergang oder die weitere Verschlechterung der Ware sowie f&uuml;r die nicht durch den Mangel eingetretene Unm&ouml;glichkeit der Herausgabe der Ware im Zeitraum zwischen Lieferung der Ware und R&uuml;cksendung der Ware hat der Kunde Wertersatz zu leisten. Der Kunde hat keinen Wertersatz f&uuml;r die durch den bestimmungsgem&auml;&szlig;en Gebrauch der Ware entstandene Verschlechterung der Ware zu leisten. Die Pflicht zum Wertersatz entf&auml;llt f&uuml;r die R&uuml;cksendung eines mangelhaften Produktes im Gew&auml;hrleistungsfall ferner, wenn sleepz die Verschlechterung oder den Untergang zu vertreten hat oder der Schaden auch bei sleepz eingetreten w&auml;re.</p>
<p>8.9 Die Schadensersatzpflicht des Kunden bei einer vom Kunden zu vertretenden Verletzung der R&uuml;cksendungspflicht richtet sich nach Ma&szlig;gabe der gesetzlichen Bestimmungen.</p>
<p>8.10 Der Kunde kann nach seiner Wahl vom Vertrag zur&uuml;cktreten oder den Kaufpreis mindern, wenn die Reparatur oder Ersatzlieferung innerhalb einer angemessenen Frist nicht zu einem vertragsgerechten Zustand des Produktes gef&uuml;hrt hat.</p>
<p>8.11 Dar&uuml;ber hinaus k&ouml;nnen auch Anspr&uuml;che gegen den Hersteller im Rahmen einer von diesem einger&auml;umten Garantie bestehen, die sich nach den entsprechenden Garantiebedingungen richten.</p>
<p>8.12 Die gesetzliche Gew&auml;hrleistung von sleepz endet zwei Jahre ab Lieferung der Ware. Die Frist beginnt mit dem Erhalt der Ware durch den Kunden.</p>
<p>8.13 Vorbehaltlich Satz 2 ist zur Geltendmachung etwaiger Gew&auml;hrleistungs- und/oder Haftungsanspr&uuml;che die Vorlage des Originalbeleges (Rechnung) erforderlich. Das Recht des Kunden, den Erwerb von sleepz anders als durch Vorlage des Originalbelegs nachzuweisen, bleibt unber&uuml;hrt.</p>
<p>&nbsp;</p>
<h5>9. R&uuml;cksendung</h5>
<p>9.1 Der Kunde sollte bei der R&uuml;cksendung der Ware und des Zubeh&ouml;rs in jedem Fall nach M&ouml;glichkeit die Originalverpackung, auch wenn diese durch eine &Ouml;ffnung zur Funktionspr&uuml;fung besch&auml;digt sein sollte, verwenden. Eine Verpflichtung des Kunden zur Verwendung der Originalverpackung besteht nicht, allerdings kann hierdurch ggf. verhindert werden, dass sleepz von dem Kunden unter Umst&auml;nden Wertersatz wegen der fehlenden Originalverpackung verlangen muss.</p>
<p>9.2 Wenn der Kunde eine unn&ouml;tig teure Versandart f&uuml;r die R&uuml;cksendung w&auml;hlt, ist der Kunde verpflichtet, die gegen&uuml;ber einer g&uuml;nstigen Versandart erh&ouml;hten Kosten an sleepz zu zahlen, wenn die Kosten der R&uuml;cksendung von sleepz zu tragen sind.</p>
<p>&nbsp;</p>
<h5>10. Andere Unternehmen, Plattformen</h5>
<p>10.1 Es ist m&ouml;glich, dass andere Unternehmen als sleepz &uuml;ber die Webseite www.perfekt-schlafen.de Dienstleistungen oder Waren verkaufen (&bdquo;Drittanbieter&ldquo;). Ferner ist es m&ouml;glich, dass sleepz Links zu Seiten von verbundenen Firmen und bestimmten anderen Unternehmen zur Verf&uuml;gung stellt. Auch wenn sleepz Betreiber des Internet-Portals www.perfekt-schlafen.de ist, ist sleepz weder K&auml;ufer noch Verk&auml;ufer solcher Drittanbieter-Leistungen; vertragliche, insbesondere kaufvertragliche, Beziehungen kommen ausschlie&szlig;lich mit dem Drittanbieter zustande. sleepz ist daher auch nicht f&uuml;r die Untersuchung und Bewertung solcher Angebote Dritter verantwortlich und sleepz leistet keine Gew&auml;hr f&uuml;r solche Angebote oder die Inhalte auf verlinkten Webseiten. sleepz &uuml;bernimmt keine Verantwortung oder Haftung f&uuml;r Handlungen, Waren und Inhalte solcher dritter Personen. Der Drittanbieter ist verantwortlich f&uuml;r den Verkauf der Drittanbieter-Leistungen, f&uuml;r jegliche Reklamationen von Seiten seiner K&auml;ufer sowie alle anderen Angelegenheiten, die durch den Vertrag mit dem Drittanbieter entstehen. sleepz unterrichtet dar&uuml;ber, wenn eine dritte Person in den Gesch&auml;ftsvorgang einbezogen ist und sleepz ist berechtigt, die Informationen des Kunden in Bezug auf diesen Gesch&auml;ftsvorgang dieser dritten Person mitzuteilen. Datenschutzerkl&auml;rungen und andere Nutzungsbedingungen solcher Dritter sind sorgf&auml;ltig zu lesen.</p>
<p>10.2 Es ist m&ouml;glich, dass der Kunde Ware von sleepz &uuml;ber Drittanbieter-Plattformen oder aufgrund von Kooperationen mit Drittanbietern (z.B. Gutscheinportalen) von sleepz zu reduzierten Kaufpreisen erwerben kann. Sofern der Kunde aufgrund der Nutzung einer Drittanbieter-Plattform unmittelbar Vertr&auml;ge mit dem Drittanbieter eingeht, ist dieser Drittanbieter als Vertragspartner f&uuml;r die Abwicklung des Vertrages zust&auml;ndig.</p>
<p>10.3 Im Fall einer R&uuml;ckabwicklung von Verk&auml;ufen, in die auf Initiative des Kunden ein Drittanbieter (z.B. ein Gutscheinportal) einbezogen wurde, der hier&uuml;ber eine vertragliche Vereinbarung mit sleepz hat, ist die R&uuml;ckabwicklung unter Einbeziehung des Drittanbieters durchzuf&uuml;hren; dies dient u.a. dazu sicherzustellen, dass Erstattungsanspr&uuml;che von sleepz gegen den Drittanbieter aufgrund der R&uuml;ckabwicklung erfasst und durch den Drittanbieter zeitnah erf&uuml;llt werden. sleepz steht im Rahmen der R&uuml;ckabwicklung ein Zur&uuml;ckbehaltungsrecht zu, bis die Einbeziehung des Drittanbieters sichergestellt ist.</p>
<p>&nbsp;</p>
<h5>11. Haftung</h5>
<p>11.1 Bei leichter Fahrl&auml;ssigkeit haftet sleepz nur bei der Verletzung vertragswesentlicher Pflichten und beschr&auml;nkt auf den vorhersehbaren Schaden. Diese Beschr&auml;nkung gilt nicht bei der Verletzung von Leben, K&ouml;rper und Gesundheit. F&uuml;r sonstige leicht fahrl&auml;ssig durch einen Mangel des Kaufgegenstandes verursachte Sch&auml;den haftet sleepz nicht.</p>
<p>11.2 Unabh&auml;ngig von einem Verschulden von sleepz bleibt eine Haftung von sleepz bei arglistigem Verschweigen des Mangels oder aus der &Uuml;bernahme einer Garantie unber&uuml;hrt. Die Herstellergarantie ist eine Garantie des Herstellers und stellt keine &Uuml;bernahme einer Garantie durch sleepz dar.</p>
<p>11.3 Ausgeschlossen ist die pers&ouml;nliche Haftung der gesetzlichen Vertreter, Erf&uuml;llungsgehilfen und Betriebsangeh&ouml;rigen von sleepz f&uuml;r von ihnen durch leichte Fahrl&auml;ssigkeit verursachte Sch&auml;den.</p>
<p>11.4 Die Regelung der 8.13 findet entsprechende Anwendung.</p>
<p>&nbsp;</p>
<h5>12. Datenschutz</h5>
<p>12.1 Die Bestell- und Adressdaten der Kunden werden gespeichert. Die Speicherung und Verwendung dieser Daten erfolgt im Rahmen der Auftragsabwicklung (auch durch &Uuml;bermittlung an die zur Auftragsabwicklung eingesetzten Partner oder Versandunternehmen), f&uuml;r eventuelle Gew&auml;hrleistungsf&auml;lle und zu eigenen Werbezwecken. Der Kunde kann der Nutzung seiner Daten f&uuml;r Werbezwecke jederzeit durch einfache Mitteilung an sleepz widersprechen.</p>
<p>12.2 Nach dem Bundesdatenschutzgesetz hat der Kunde ein Recht auf unentgeltliche Auskunft &uuml;ber seine gespeicherten Daten sowie ggf. ein Recht auf Berichtigung, Sperrung oder L&ouml;schung dieser Daten. Bei Fragen zur Erhebung, Verarbeitung oder Nutzung der personenbezogenen Daten, bei Ausk&uuml;nften, Berichtigung, Sperrung oder L&ouml;schung von Daten wendet sich der Kunde bitte an:</p>
<p>sleepz GmbH<br /> Seestra&szlig;e 35<br /> 14974 Ludwigsfelde</p>
<p>&nbsp;</p>
<h5>13. Schlussbestimmungen</h5>
<p>13.1 Es gilt das Recht der Bundesrepublik Deutschland. Bei Verbrauchern, die den Vertrag nicht zu beruflichen oder gewerblichen Zwecken abschlie&szlig;en, gilt diese Rechtswahl nur insoweit, als nicht der gew&auml;hrte Schutz durch zwingende Bestimmungen des Rechts des Staates, in dem der Verbraucher seinen gew&ouml;hnlichen Aufenthalt hat, entzogen wird.</p>
<p>13.2 Die Bestimmungen des &Uuml;bereinkommens der Vereinten Nationen &uuml;ber Vertr&auml;ge &uuml;ber den internationalen Warenkauf (UN-Kaufrecht) finden keine Anwendung.</p>
<p>13.3 Sofern Kunden entgegen ihren Angaben bei der Bestellung keinen Wohnsitz in der Bundesrepublik Deutschland haben oder nach Vertragsabschluss ihren Wohnsitz ins Ausland verlegen oder der Wohnsitz zum Zeitpunkt der Klageerhebung nicht bekannt ist, ist Gerichtsstand f&uuml;r alle Streitigkeiten aus und im Zusammenhang mit dem Vertragsverh&auml;ltnis Berlin.</p>
<p>13.4 Die Geltung zwingender gesetzlicher Bestimmungen bleibt unber&uuml;hrt.</p>
<p>13.5 Sollten einzelne Bestimmungen des Vertrages oder dieser AGB ganz oder teilweise unwirksam oder nichtig oder undurchf&uuml;hbar sein oder werden, so wird dadurch die Wirksamkeit des Vertrages und der AGB im &Uuml;brigen nicht ber&uuml;hrt. sleepz und der Kunde verpflichten sich, die unwirksame oder nichtige oder undurchf&uuml;hbare Bestimmung durch eine wirksame Bestimmung zu ersetzen, die dem gewollten wirtschaftlichen Zweck am n&auml;chsten kommt. Dasselbe gilt im Fall einer L&uuml;cke.</p>
<p>13.6 sleepz beh&auml;lt sich das Recht vor, jederzeit und ohne Einschr&auml;nkungen &Auml;nderungen der eigenen Regeln und Bedingungen inklusive dieser AGB und der Versandinformationen vorzunehmen. Der Kunde unterliegt den Bedingungen inklusive der AGB, die zu dem Zeitpunkt in Kraft sind, in dem der Kunde gegen&uuml;ber sleepz ein Angebot abgibt. Sollte eine der Bedingungen, insbesondere auch aus diesen AGB, f&uuml;r nichtig, unwirksam oder f&uuml;r undurchf&uuml;hrbar gehalten werden, gilt diese Regelung/Bedingung in jedem Fall als abtrennbar und beeinflusst die G&uuml;ltigkeit und Durchf&uuml;hrbarkeit aller verbleibenden Regelungen und Bedingungen nicht.</p>
<p>13.7 sleepz beh&auml;lt sich vor, im Einzelfall aus Gr&uuml;nden der Kulanz von einzelnen Bestimmungen dieser AGB zu Gunsten des Kunden abzuweichen. Ein Anspruch des Kunden auf solche Abweichungen aus Kulanzgr&uuml;nden besteht nicht; die Entscheidung liegt stets im freien Ermessen von sleepz; dies gilt z.B. auch dann, wenn sleepz in der Vergangenheit gegen&uuml;ber dem oder den gleichen Kunden oder in &auml;hnlichen Situationen bereits einmal oder mehrfach aus Kulanzgr&uuml;nden von den Bestimmungen dieser AGB abgewichen ist.</p>
<p>13.8 Sollten Kunden diese AGB verletzen und sleepz hiergegen nichts unternehmen, so liegt darin in keinem Fall ein Verzicht seitens sleepz auf irgendwelche Rechte; vielmehr ist sleepz weiterhin berechtigt, von seinen Rechten bei jeder Gelegenheit, in der Kunden diese AGB verletzten oder verletzt haben, Gebrauch zu machen.</p>
<p>&nbsp;</p>
<p>sleepz GmbH<br /> vertreten durch Gesch&auml;ftsf&uuml;hrer Youssef Hassan<br /> Seestra&szlig;e 35<br /> 14974 Ludwigsfelde<br /> Deutschland</p>
<p>Telefon: {{customVar code=ps-telefon}}<br /> Telefax: {{customVar code=ps-fax}}<br /> E-Mail: support@perfekt-schlafen.de<br /> USt-IdNr.: DE815413810<br /> Eingetragen beim Amtsgericht Potsdam, HRB 27538 P</p>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('AGB');
$cmsBlock->setIdentifier('gs_business_terms');
$cmsBlock->setContent($blockcontent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();


$blockcontent = <<<EOF
<h2> Widerrufsrecht für Verbraucher</h2>
<p>Nachfolgend erhalten Sie eine Belehrung über die Voraussetzungen und Folgen des gesetzlichen Widerrufsrechts bei Versandbestellungen. Eine über das Gesetz hinausgehende vertragliche Einräumung von Rechten ist damit nicht verbunden. Insbesondere steht das gesetzliche Widerrufsrecht nicht gewerblichen Wiederverkäufern zu.</p>

<div class="wr-border">
<p><h3>Widerrufsbelehrung</h3></p>

<p><strong>Widerrufsrecht</strong></p>

<p>Sie haben das Recht, binnen vierzehn Tagen ohne Angabe von Gründen diesen Vertrag zu widerrufen.</p>

<p>Die Widerrufsfrist beträgt vierzehn Tage ab dem Tag, an dem Sie oder ein von Ihnen benannter Dritter, der nicht der Beförderer ist, die letzte Ware in Besitz genommen haben bzw. hat.</p>

<p>Um Ihr Widerrufsrecht auszuüben, müssen Sie uns (sleepz GmbH,Seestraße 35, 14974 Ludwigsfelde, Tel. {{customVar code=ps-telefon}}, Fax. {{customVar code=ps-fax}}, E-Mail: {{customVar code=ps-supportmail}}) mittels einer eindeutigen Erklärung (z. B. ein mit der Post versandter Brief, Telefax oder E-Mail) über Ihren Entschluss, diesen Vertrag zu widerrufen, informieren. Sie können dafür das beigefügte Muster-Widerrufsformular verwenden, das jedoch nicht vorgeschrieben ist.</p>

<p>Zur Wahrung der Widerrufsfrist reicht es aus, dass Sie die Mitteilung über die Ausübung des Widerrufsrechts vor Ablauf der Widerrufsfrist absenden.</p>

<p><strong>Folgen des Widerrufs</strong></p>

<p>Wenn Sie diesen Vertrag widerrufen, haben wir Ihnen alle Zahlungen, die wir von Ihnen erhalten haben, einschließlich der Lieferkosten (mit Ausnahme der zusätzlichen Kosten, die sich daraus ergeben, dass Sie eine andere Art der Lieferung als die von uns angebotene, günstigste Standardlieferung gewählt haben), unverzüglich und spätestens binnen vierzehn Tagen ab dem Tag zurückzuzahlen, an dem die Mitteilung über Ihren Widerruf dieses Vertrags bei uns eingegangen ist. Für diese Rückzahlung verwenden wir dasselbe Zahlungsmittel, das Sie bei der ursprünglichen Transaktion eingesetzt haben, es sei denn, mit Ihnen wurde ausdrücklich etwas anderes vereinbart; in keinem Fall werden Ihnen wegen dieser Rückzahlung Entgelte berechnet. </p>

<p>Sie müssen für einen etwaigen Wertverlust der Waren nur aufkommen, wenn dieser Wertverlust auf einen zur Prüfung der Beschaffenheit, Eigenschaften und Funktionsweise der Waren nicht notwendigen Umgang mit ihnen zurückzuführen ist.</p>

<p><strong>Für Waren, die normal mit der Post zurückgesendet werden können, gilt:</strong><br />
Wir können die Rückzahlung verweigern, bis wir die Waren wieder zurückerhalten haben oder bis Sie den Nachweis erbracht haben, dass Sie die Waren zurückgesandt haben, je nachdem, welches der frühere Zeitpunkt ist. </p>

<p>Sie haben die Waren unverzüglich und in jedem Fall spätestens binnen vierzehn Tagen ab dem Tag, an dem Sie uns über den Widerruf dieses Vertrags unterrichten, an sleepz GmbH, Brandenburg Park, Seestraße 35, 14974 Genshagen zurückzusenden oder zu übergeben. Die Frist ist gewahrt, wenn Sie die Waren vor Ablauf der Frist von vierzehn Tagen absenden. Wir tragen die Kosten der Rücksendung der Waren.</p>

<p><strong>Für Waren, die nicht normal mit der Post zurückgesendet werden können (nicht paketfähige Matratzen, Lattenroste, Schlafsofas, Betten und nicht paketfähige Möbel), gilt:</strong><br />
Wir holen die Waren ab. Wir tragen die Kosten der Rücksendung der Waren. </p>
</div>

<p><strong>Ausnahmen vom Widerrufsrecht</strong></p>
<p>Es existieren gesetzliche Ausnahmen vom Widerrufsrecht (§ 312g BGB), wobei wir uns vorbehalten, uns Ihnen gegenüber auf folgende Regelungen zu berufen:<br />
Ein Widerrufsrecht besteht nicht bei Verträgen zur Lieferung von Waren, die nicht vorgefertigt sind und für deren Herstellung eine individuelle Auswahl oder Bestimmung durch den Verbraucher maßgeblich ist oder die eindeutig auf die persönlichen Bedürfnisse des Verbrauchers zugeschnitten sind.</p>

<p><strong>Muster für das Widerrufsformular</strong></p>

<div class="wr-border">
<p><strong>Muster-Widerrufsformular</strong></p>
<p>(Wenn Sie den Vertrag widerrufen wollen, dann füllen Sie bitte dieses Formular aus und senden Sie es zurück.)</p>

<p>- An sleepz GmbH, Seestraße 35, 14974 Ludwigsfelde, Tel. {{customVar code=ps-telefon}}, Fax. {{customVar code=ps-fax}}, E-Mail: {{customVar code="ps-supportmail}}:</p>

<p>- Hiermit widerrufe(n) ich/wir (*) den von mir/uns (*) abgeschlossenen Vertrag über den Kauf der folgenden Waren (*)/die Erbringung der folgenden Dienstleistung (*)</p>

<p>- Bestellt am (*)/erhalten am (*)</p>

<p>- Name des/der Verbraucher(s)</p>

<p>- Anschrift des/der Verbraucher(s)</p>

<p>- Unterschrift des/der Verbraucher(s) (nur bei Mitteilung auf Papier)</p>

<p>- Datum</p>
________
<p>(*) Unzutreffendes streichen.</p>
</div>
<br />
<p><a href="{{store url=""}}media/widerruf/Widerrufsformular-PS.pdf" onclick="window.open(this.href); return false;">Hier geht es zum Online Formular</a></p>
EOF;

$cmsBlock = Mage::getModel('cms/block');
$cmsBlock->setTitle('Legal Widerrufsbelehrung');
$cmsBlock->setIdentifier('gs_revocation');
$cmsBlock->setContent($blockcontent);
$cmsBlock->setIsActive(true);
$cmsBlock->setStores(array($scopeId));
$cmsBlock->save();