-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Gen 25, 2021 alle 13:02
-- Versione del server: 10.4.11-MariaDB
-- Versione PHP: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blogdb`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `argomenti`
--

CREATE TABLE `argomenti` (
  `id` int(10) UNSIGNED NOT NULL,
  `idParent` int(10) NOT NULL,
  `argomento` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `argomenti`
--

INSERT INTO `argomenti` (`id`, `idParent`, `argomento`) VALUES
(601, 0, 'cibo'),
(602, 0, 'alimenti'),
(603, 0, 'nutrizione'),
(604, 0, 'basket'),
(605, 604, 'americano'),
(606, 0, 'nba'),
(607, 0, 'Spike'),
(608, 607, 'Lee'),
(609, 0, 'film'),
(610, 609, 'americani'),
(611, 0, 'album'),
(612, 611, '2020'),
(613, 0, 'musica');

-- --------------------------------------------------------

--
-- Struttura della tabella `argomentiblog`
--

CREATE TABLE `argomentiblog` (
  `id` int(10) UNSIGNED NOT NULL,
  `idArgomenti` int(10) UNSIGNED NOT NULL,
  `idBlog` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `argomentiblog`
--

INSERT INTO `argomentiblog` (`id`, `idArgomenti`, `idBlog`) VALUES
(844, 601, 358),
(845, 602, 358),
(846, 603, 358),
(847, 604, 359),
(848, 605, 359),
(849, 606, 359),
(854, 607, 360),
(855, 608, 360),
(856, 609, 360),
(857, 610, 360),
(876, 611, 361),
(877, 612, 361),
(878, 613, 361);

-- --------------------------------------------------------

--
-- Struttura della tabella `blog`
--

CREATE TABLE `blog` (
  `id` int(10) UNSIGNED NOT NULL,
  `nomeBlog` varchar(128) NOT NULL,
  `autore` varchar(128) NOT NULL,
  `descrizione` varchar(128) NOT NULL,
  `dataCreazione` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `blog`
--

INSERT INTO `blog` (`id`, `nomeBlog`, `autore`, `descrizione`, `dataCreazione`) VALUES
(358, 'i miei cibi preferiti', 'mario_rossi', 'In questo blog presenterò alcuni dei miei cibi preferiti, con allegate foto di piatti e le loro descrizioni!', '2021-01-21 12:24:08'),
(359, 'dream team NBA', 'michael23', 'In questo blog pubblicherò il mio dream team di giocatori NBA, prendendo giocatori di tutte le ere.', '2021-01-21 12:48:14'),
(360, 'Film di Spike Lee', 'michael23', 'In questo blog posterò quali sono per me i migliori film del regista americano Spike Lee.', '2021-01-21 13:03:57'),
(361, 'migliori album 2020', 'dominic', 'In questo blog scriverò quali sono stati, secondo me, i migliori album del 2020.', '2021-01-21 13:44:32');

-- --------------------------------------------------------

--
-- Struttura della tabella `commenti`
--

CREATE TABLE `commenti` (
  `id` int(10) UNSIGNED NOT NULL,
  `contenuto` varchar(5000) NOT NULL,
  `autore` varchar(128) NOT NULL,
  `idPost` int(10) UNSIGNED NOT NULL,
  `dataCreazione` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `commenti`
--

INSERT INTO `commenti` (`id`, `contenuto`, `autore`, `idPost`, `dataCreazione`) VALUES
(135, 'Bellissimo film!', 'gabe', 153, '2021-01-21 14:03:16'),
(136, 'Meglio Malcolm X', 'gabe', 154, '2021-01-21 14:03:38'),
(137, 'Pizza tutta la vita', 'gabe', 143, '2021-01-21 14:03:53'),
(138, 'Bellissimo post!!', 'gabe', 146, '2021-01-21 14:04:06'),
(139, 'bello!', 'gabe', 147, '2021-01-21 14:04:39'),
(141, '24/8', 'gabe', 148, '2021-01-21 14:05:32'),
(142, 'Hakeem The Dream!', 'gabe', 151, '2021-01-21 14:05:45'),
(143, 'His Airness', 'gabe', 150, '2021-01-21 14:05:57'),
(144, 'miglior album 2020', 'gabe', 157, '2021-01-21 14:06:23'),
(146, '...buona!', 'luca', 145, '2021-01-21 14:08:14'),
(148, 'concordo\n', 'luca', 154, '2021-01-21 14:09:22'),
(150, 'rip kobe!', 'luca', 148, '2021-01-21 14:09:58'),
(151, 'bellissimo album', 'luca', 161, '2021-01-21 14:10:17');

-- --------------------------------------------------------

--
-- Struttura della tabella `immagini`
--

CREATE TABLE `immagini` (
  `id` int(10) UNSIGNED NOT NULL,
  `directory` varchar(128) NOT NULL,
  `idPost` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `immagini`
--

INSERT INTO `immagini` (`id`, `directory`, `idPost`) VALUES
(73, 'pics/358/143/maxresdefault.jpg', 143),
(74, 'pics/358/145/1280px-Bistecca_alla_fiorentina_(400gr).jpg', 145),
(75, 'pics/358/146/1280px-Risotto_alla_Milanese.jpg', 146),
(76, 'pics/358/147/La_Farinata_di_ceci.jpg', 147),
(77, 'pics/359/148/Kobe+Bryant+Boston+Celtics+v+Los+Angeles+Lakers+MuRC3UPqZ3fl.jpg', 148),
(78, 'pics/359/149/magic_pass.jpg', 149),
(79, 'pics/359/150/921ab403a7b0a2ae15727ba0655276fe.jpg', 150),
(81, 'pics/359/151/maxresdefault (1).jpg', 151),
(82, 'pics/359/152/960x0.jpg', 152),
(84, 'pics/360/153/he_got_game_-_h_-_1998-928x523.jpg', 153),
(85, 'pics/360/154/021816_do-the-right-thing-1-590x320.jpg', 154),
(86, 'pics/360/155/griotmag-blackkklansman_spike-lee-recensione-1024x683-1024x682.jpg', 155),
(87, 'pics/361/156/Dominic_fike_what_could_possibly_go_wrong.jpg', 156),
(88, 'pics/361/157/71ffMedtKNL._AC_SX466_.jpg', 157),
(89, 'pics/361/158/Tame_Impala_-_The_Slow_Rush.png', 158),
(90, 'pics/361/159/6660b3f0ab189a55ca4b17063115ab91.1000x1000x1.jpg', 159),
(91, 'pics/361/160/qvhwym-chipchrome-preview-m3.jpg', 160),
(92, 'pics/361/161/Amine_Limbo.jpg', 161);

-- --------------------------------------------------------

--
-- Struttura della tabella `partecipanti`
--

CREATE TABLE `partecipanti` (
  `id` int(10) UNSIGNED NOT NULL,
  `nomeUtente` varchar(30) NOT NULL,
  `idBlog` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `partecipanti`
--

INSERT INTO `partecipanti` (`id`, `nomeUtente`, `idBlog`) VALUES
(429, 'mario_rossi', 358),
(430, 'michael23', 359),
(431, 'michael23', 360),
(432, 'dominic', 361),
(440, 'michael23', 361);

-- --------------------------------------------------------

--
-- Struttura della tabella `personalizzazione`
--

CREATE TABLE `personalizzazione` (
  `id` int(10) UNSIGNED NOT NULL,
  `idBlog` int(10) UNSIGNED NOT NULL,
  `temaBlog` varchar(128) NOT NULL,
  `fontBlog` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `personalizzazione`
--

INSERT INTO `personalizzazione` (`id`, `idBlog`, `temaBlog`, `fontBlog`) VALUES
(348, 359, 'Tema Scuro', 'Open Sans'),
(350, 360, 'Tema Scuro', 'Noto Sans JP'),
(355, 361, 'Tema Verde', 'Roboto (default)');

-- --------------------------------------------------------

--
-- Struttura della tabella `post`
--

CREATE TABLE `post` (
  `id` int(10) UNSIGNED NOT NULL,
  `idBlog` int(10) UNSIGNED NOT NULL,
  `titolo` varchar(128) NOT NULL,
  `contenuto` varchar(20000) CHARACTER SET utf8 NOT NULL,
  `autore` varchar(128) NOT NULL,
  `dataCreazione` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `post`
--

INSERT INTO `post` (`id`, `idBlog`, `titolo`, `contenuto`, `autore`, `dataCreazione`) VALUES
(143, 358, 'Pizza', 'La pizza è un prodotto gastronomico salato che consiste in un impasto a base di farina, acqua e lievito che viene spianato e condito tipicamente con pomodoro, mozzarella e altri ingredienti e cotto in un forno a legna. Originario della cucina napoletana, è oggi, insieme alla pasta, l\'alimento italiano più conosciuto all\'estero\r\nCol nome pizza, praticamente ignoto al di là della cinta urbana napoletana, ancora nel XVIII secolo, si indicavano le torte, quasi sempre dolci. Fu solo a partire dagli inizi del XIX secolo che la pizza assunse, sempre a Napoli, la sua attuale connotazione. Il seguente successo planetario della pietanza ha portato, per estensione, a definire nello stesso modo qualsiasi preparazione analoga\r\nNel 2017 l\'UNESCO ha dichiarato l\'arte del pizzaiuolo napoletano come patrimonio immateriale dell\'umanità\r\nPizza è la parola italiana più famosa al mondo!!', 'mario_rossi', '2021-01-21 12:27:50'),
(145, 358, 'Bistecca alla fiorentina', 'La bistecca alla fiorentina, chiamata anche semplicemente fiorentina, è un taglio di carne di vitellone o di scottona che, unito alla specifica preparazione, ne fa uno dei piatti più conosciuti della cucina toscana. Si tratta di un taglio alto comprensivo dell\'osso, da cuocersi sulla brace o sulla griglia, con grado di cottura \"al sangue\".\r\nLa bistecca alla fiorentina si ottiene dal taglio della lombata (la parte in corrispondenza alle vertebre lombari, la metà della schiena dalla parte della coda) di vitellone o scottona: ha nel mezzo l\'osso a forma di \"T\", in inglese infatti è chiamata T-bone steak, con il filetto da una parte e il controfiletto dall\'altra.\r\nL\'esperto culinario Pellegrino Artusi, nel suo manuale La scienza in cucina e l\'arte di mangiar bene, così definisce il taglio della bistecca: «Bistecca alla fiorentina. Da beef-steak, parola inglese che vale costola di bue, è derivato il nome della nostra bistecca, la quale non è altro che una braciuola col suo osso, grossa un dito o un dito e mezzo, tagliata dalla lombata di vitella».', 'mario_rossi', '2021-01-21 12:34:01'),
(146, 358, 'Risotto alla milanese', 'Il risotto alla milanese (ris giald in dialetto milanese), è, insieme alla cotoletta alla milanese e al panettone, il piatto più tipico e conosciuto di Milano. Si tratta di un risotto i cui ingredienti principali, oltre a quelli necessari per preparare un risotto in bianco, sono lo zafferano, dal quale deriva il suo caratteristico colore giallo, e il midollo di bue. Può essere servito anche come contorno dell\'ossobuco, altro piatto tipico milanese.\r\nLe origini del risotto alla milanese risalgono al Medioevo e sono collegate a un\'analoga ricetta della cucina araba e della cucina ebraica. Nel Medioevo, in Italia, questa pietanza era conosciuta come riso col zafran.\r\n\r\nIl risotto alla milanese nacque nel 1574 alla tavola del vetraio belga Valerio di Fiandra, che all\'epoca risiedeva a Milano poiché stava lavorando alle vetrate del Duomo di Milano. Per il matrimonio di sua figlia i suoi colleghi vetrai fecero aggiungere a un risotto bianco al burro dello zafferano: questa spezia era infatti utilizzata dai vetrai per ottenere una particolare colorazione gialla dei vetri. Il nuovo piatto ebbe subito successo, sia per il suo sapore che per la sua tonalità gialla, che ricordava l\'oro, sinonimo di ricchezza. Lo zafferano ha anche riconosciute proprietà farmacologiche e quindi il risotto giallo si diffuse presto nelle osterie e nelle taverne milanesi.', 'mario_rossi', '2021-01-21 12:38:11'),
(147, 358, 'Farinata di ceci', 'La farinata di ceci, conosciuta anche come fainè, fainà (in ligure) o cecìna, è una torta salata molto bassa, preparata con farina di ceci, acqua, sale e olio extravergine di oliva.\r\n\r\nSi tratta di un piatto italiano tipico della tradizione ligure e anche toscana. In Liguria prende il nome di fainâ. A Pisa, Lucca e in Versilia è conosciuta con l\'appellativo di cecìna, a Livorno come torta di ceci o, più semplicemente, torta, come \"calda calda\" a Massa e a Carrara, come socca a Nizza o come cade a Tolone, in Francia, nel Basso Piemonte e in particolare nella provincia di Alessandria dove nel Tortonese è anche chiamata belécauda. Viene preparata anche in Sardegna soprattutto a Sassari dove il suo nome è fainè (spesso, cotta con altri ingredienti come cipolle, acciughe, salsiccia e con l\'aggiunta di una spolverata di pepe nero; da qui si è diffusa in parte della Sardegna settentrionale, specialmente ad Alghero e a Porto Torres), oltre a Carloforte e a Calasetta. La farinata si è diffusa anche in alcune località all\'estero, come a Buenos Aires in Argentina, e a Montevideo in Uruguay dove è conosciuta come fainá. Famosissima e presente in tutte le pizzerie di tutta la provincia di Ferrara e nel vicino Polesine dove viene chiamata padellata di ceci e servita con sale e pepe oppure con salsiccia spezzettata e cipolla.\r\n\r\nSi cuoce in un forno a legna, in teglia, a 300 °C per 10 minuti e assume con la cottura una crosticina con un vivace colore dorato, mentre sotto rimane liscia e senza crosta. Alcune aziende alimentari ne propongono una versione confezionata precotta, pronta da scaldare e venduta nella grossa distribuzione.', 'mario_rossi', '2021-01-21 12:39:28'),
(148, 359, 'Kobe Bryant', 'Kobe Bean Bryant (Filadelfia, 23 agosto 1978) è stato un cestista statunitense.\r\n\r\nHa giocato prevalentemente nel ruolo di guardia tiratrice ed è considerato tra i migliori giocatori della storia dell\'NBA. Figlio di Joe Bryant e nipote da parte di madre di Chubby Cox, entrambi ex giocatori di basket, Bryant è cresciuto cestisticamente in Italia, dove ha imparato i fondamentali europei, e ha disputato tutta la sua carriera professionistica nei Los Angeles Lakers, conquistando 5 titoli; è stato inoltre il primo giocatore NBA a militare nella stessa squadra per 20 stagioni. Con la Nazionale statunitense ha partecipato ai FIBA Americas Championship 2007 e ai Giochi olimpici di Pechino 2008 e di Londra 2012, vincendo la medaglia d\'oro in tutte e tre le manifestazioni.\r\n\r\nIl 4 marzo 2018 ha vinto il Premio Oscar insieme al regista e animatore Glen Keane, nella categoria miglior cortometraggio d\'animazione per Dear Basketball, che ha sceneggiato ispirandosi alla sua lettera di addio al basket.\r\n\r\nRientra tra gli sportivi più conosciuti al mondo e la sua carriera è ritenuta una delle migliori nella storia dello sport professionistico.\r\n\r\nIl 4 aprile 2020 è stato inserito tra i membri del Naismith Memorial Basketball Hall of Fame.', 'michael23', '2021-01-21 12:50:24'),
(149, 359, 'Magic Johnson', 'Earvin Johnson Jr., meglio conosciuto come Magic Johnson (Lansing, 14 agosto 1959), è un ex cestista, allenatore di pallacanestro, imprenditore e dirigente sportivo statunitense, considerato uno dei più grandi giocatori della storia della pallacanestro.\r\n\r\nHa vinto cinque titoli NBA con i Los Angeles Lakers, l\'oro alle Olimpiadi 1992 e al Tournament of the Americas 1992 con il Dream Team statunitense, nonché un titolo NCAA con Michigan State nel 1979. È stato eletto tre volte miglior giocatore NBA e miglior giocatore delle finali NBA. Il suo nome figura nel Naismith Memorial Basketball Hall of Fame e nella lista dei 50 migliori giocatori della storia NBA. La sua maglia numero 32 è stata ufficialmente ritirata dai Lakers il 16 febbraio 1992.\r\n\r\nMagic Johnson è stato capace di rivoluzionare la pallacanestro: giocò infatti da playmaker, un ruolo tradizionalmente riservato al giocatore più basso e agile di una squadra. Con i suoi 206 centimetri di altezza è stato il play più alto nella storia della NBA, ma al tempo stesso si è dimostrato un giocatore dinamico e dotato di un\'eccellente visione di gioco: è divenuto celebre per le doti nel palleggio, i passaggi dietro la schiena, gli alley-oop e i passaggi no-look. Nel corso degli anni ottanta è stato protagonista di un\'accesa rivalità sportiva con l\'ala dei Boston Celtics Larry Bird. Fino al 1992, anno del ritiro di Bird, si divideranno in totale otto titoli NBA.', 'michael23', '2021-01-21 12:52:45'),
(150, 359, 'Michael Jordan', 'Michael Jeffrey Jordan, conosciuto anche con le sue iniziali, MJ, o semplicemente come Michael Jordan( New York, 17 febbraio 1963), è un ex cestista statunitense, nonché principale azionista e presidente della squadra di pallacanestro degli Charlotte Hornets.\r\n\r\nSoprannominato Air Jordan e His Airness per le sue qualità atletiche e tecniche, fu eletto nel 1999 \"il più grande atleta nord-americano del XX secolo\" dal canale televisivo sportivo ESPN. La fama acquisita sul campo lo ha reso un\'icona dello sport, al punto da spingere la Nike a dedicargli una linea di scarpe da pallacanestro chiamata Air Jordan, introdotta nel 1984.\r\n\r\nGiocò per tre anni all\'Università della Carolina del Nord a Chapel Hill, dove guidò la squadra alla vittoria del campionato nazionale NCAA nel 1982. Fu poi scelto per terzo al Draft NBA 1984 dai Chicago Bulls e diventò in breve tempo una delle stelle della lega, contribuendo a diffondere la NBA a livello mondiale negli anni \'80 e \'90. Nel 1991 vinse il suo primo titolo NBA con i Bulls, per poi ripetersi con altri due successi nel 1992 e nel 1993, aggiudicandosi un three-peat, dopo il quale si ritirò per intraprendere una carriera nel baseball. Tornò ai Bulls nel 1995 e li condusse alla vittoria di un altro three-peat (1996, 1997 e 1998). Si ritirò una seconda volta nel 1999, per poi tornare come membro dei Washington Wizards dal 2001 al 2003, per poi ritirarsi definitivamente.\r\n\r\nI riconoscimenti ottenuti a livello individuale includono sei MVP delle finali, dieci titoli di miglior marcatore (entrambi record), cinque MVP della regular season, dieci selezioni All-NBA First Team e nove nell\'All-Defensive First Team, quattordici partecipazioni all\'NBA All-Star Game, tre MVP dell\'All-Star Game e un NBA Defensive Player of the Year Award. Detiene i record NBA per la media punti più alta nella storia della regular season (30,12 punti a partita) e nella storia dei playoffs (33,45 punti a partita).\r\n\r\nFu introdotto due volte nella Naismith Memorial Basketball Hall of Fame: nel 2009 per la sua carriera individuale e nel 2010 come membro del Dream Team. Diventò membro della FIBA Hall of Fame nel 2015. Il 22 novembre 2016 fu insignito dal presidente USA Barack Obama della Presidential Medal of Freedom, la più alta onorificenza civile statunitense.', 'michael23', '2021-01-21 12:55:57'),
(151, 359, 'Hakeem Olajuwon', 'Hakeem Abdul Olajuwon (Lagos, 21 gennaio 1963) è un ex cestista nigeriano naturalizzato statunitense.\r\n\r\nSoprannominato The Dream, è annoverato tra i massimi rappresentanti del ruolo del Centro, contribuendo anche alla formazione delle generazioni a venire. È considerato uno dei migliori cestisti di ogni epoca. È uno dei quattro giocatori ad aver realizzato una quadrupla doppia in NBA.\r\n\r\nAlto 213 cm per 116 kg, Hakeem è stato un centro molto versatile, in grado di offrire un ottimo contributo sia in attacco che in difesa: il suo repertorio comprendeva movimenti di altissimo livello tecnico e stilistico in post, una notevole abilità a rimbalzo e nelle stoppate nonché una spiccata propensione ai recuperi.[2] A detta di Michael Jordan, nessun altro pivot è stato in grado di eguagliare la sua completezza; in termini simili si è espresso anche Robert Horry, suo compagno agli Houston Rockets, affermando che Olajuwon sia stato il miglior interprete dei ruoli di centro e ala grande nella storia del basket.', 'michael23', '2021-01-21 12:58:26'),
(152, 359, 'Lebron James', 'LeBron Raymone James Sr., meglio noto come LeBron James (Akron, 30 dicembre 1984) è un cestista statunitense, che gioca come playmaker, ala piccola o ala grande nei Los Angeles Lakers.\r\n\r\nConosciuto anche con l\'acronimo LBJ e con vari soprannomi tra cui King James (Re James) o The Chosen One (Il prescelto), è considerato uno dei migliori cestisti di tutti i tempi. È stato selezionato con la prima scelta assoluta al Draft NBA 2003 dai Cleveland Cavaliers e nominato NBA Rookie of the Year nel 2004. In carriera ha vinto quattro volte il titolo NBA su dieci finali disputate (due con i Miami Heat, uno con i Cleveland Cavaliers e uno con i Los Angeles Lakers), quattro volte l\'NBA Finals Most Valuable Player e quattro volte l\'NBA Most Valuable Player Award.\r\n\r\nÈ al 2020 il terzo miglior marcatore della storia NBA nella stagione regolare dietro Karl Malone e Kareem Abdul-Jabbar, oltre che il secondo nelle Finali dopo Jerry West. Ai playoff detiene vari record tra cui quello di punti segnati, palle rubate, rimbalzi difensivi e vittorie.\r\n\r\nCon la Nazionale statunitense ha partecipato a tre Olimpiadi, vincendo la medaglia di bronzo ai Giochi di Atene 2004 e la medaglia d\'oro a Pechino 2008 e Londra 2012.', 'michael23', '2021-01-21 13:01:27'),
(153, 360, 'He Got Game', 'He Got Game è un film del 1998, scritto e diretto da Spike Lee.\r\nIl film presenta molti camei di veri cestisti professionisti statunitensi, tra cui Michael Jordan e Shaquille O\'Neal.\r\nFu presentato fuori concorso alla Mostra di Venezia.\r\n\r\nPrimavera, 1998. Jake Shuttlesworth è rinchiuso da più di sei anni nel carcere di Attica, presso New York, per scontare una condanna di venti anni di reclusione per l\'omicidio preterintenzionale della moglie. Fuori dal carcere, suo figlio diciottenne Jesus è considerato la promessa più grande del basket liceale e le più prestigiose università degli Stati Uniti d\'America sono disposte a tutto pur di averlo nelle loro squadre a partire dalla stagione successiva. Jesus, che con la squadra del suo liceo ha vinto da poco il campionato statale, non ha ancora compiuto la scelta e manca solo una settimana allo scadere dei termini. Per questo motivo, Jake viene convocato dal direttore del carcere che gli propone, da parte del governatore dello Stato di New York, una forte riduzione di pena se riuscirà a convincere suo figlio ad iscriversi all\'Università di Big State, della quale il governatore è accesissimo sostenitore.\r\n\r\nPer permettere l\'incontro tra padre e figlio, Jake viene costretto a mangiare del cibo avariato ed allontanato in segreto dal carcere per motivi di salute. A questo punto, Jake ha una settimana di tempo per convincere il figlio Jesus ad iscriversi all\'Università di Big State; a vegliare su Jake ci sono gli agenti Crudup e Spivey.\r\n\r\nJake incontra subito sua figlia Mary, che va alle scuole medie ed è molto felice di rivederlo. Molto diversa è la reazione di Jesus, che rifiuta di ospitare suo padre, lo tratta con astio e diffidenza e non accetta di parlargli.\r\n\r\nJake così si reca in un albergo a Coney Island, dove viveva con la famiglia e dove sono cresciuti i figli, un quartiere problematico di Brooklyn in cui una delle attività più diffuse è la pallacanestro da strada e i giovani passano ore sui playground. Qui conosce Dakota Burns, una prostituta, continuamente picchiata dal suo protettore, Sweetness. Jake si innamora presto della donna e farà quel che può per farla uscire dalla sua drammatica situazione.\r\n\r\nNel frattempo, Jesus vive una settimana molto difficile. Il ragazzo è infatti pressato da quasi tutte le persone che gli sono vicine, tra cui gli zii con cui è cresciuto dopo la morte della madre e la condanna del padre, il suo allenatore e anche la fidanzata Lala, che arriva addirittura ad accettare denaro per esercitare la sua influenza sul ragazzo. Riceve la visita di controversi procuratori sportivi che lo spingono a entrare direttamente nella NBA e di università che lo portano a visitare il loro campus per invogliarlo nella fatidica scelta. Jesus resta nell\'indecisione: ha la sorella minore a cui badare, vive in un quartiere povero e diventare subito un giocatore professionista gli risolverebbe subito i problemi economici, privandolo però della possibilità di avere un\'istruzione universitaria e con il rischio di venire circondato di parassiti e di cadere nei mille pericoli dello sport professionistico. Tutti sentono l\'odore dei soldi che Jesus guadagnerà: la situazione lo disgusta non poco e continua a chiudersi in se stesso, rimandando la decisione alla conferenza stampa dell\'ultimo momento.\r\n\r\nJake cerca invano di stabilire un dialogo con il figlio, raccontandogli tra le varie cose anche il perché del suo particolare nome: Jesus in onore del suo idolo \"Black Jesus\", come era chiamato il cestista Earl Monroe, campione NBA con i New York Knicks nel 1973 e in passato stella dei Baltimore Bullets. L\'ultima sera prima del termine della settimana di semilibertà, confessa finalmente al figlio il motivo per cui è uscito di prigione, e lo sfida a basket in un duello uno-contro-uno per costringerlo a scegliere l\'Università di Big State: se vince il padre, Jesus si iscriverà a Big State; se vince il figlio, farà quello che meglio crede. Durante la prova, nel playground vicino a casa, padre e figlio si confesseranno i loro più reconditi segreti, instaurando finalmente un dialogo per anni assente. La sfida viene facilmente vinta da Jesus, che davanti a Crudup e Spivey, sopraggiunti per riportarlo al carcere di Attica, getta davanti al padre le carte della sua iscrizione a Big State.\r\n\r\nTuttavia, commosso dalla sincerità e dall\'affetto che il padre prova per lui nonostante i suoi rifiuti, Jesus accetta comunque di iscriversi a Big State e lo comunica ufficialmente durante la conferenza stampa al suo liceo, mentre Jake, ora tornato in carcere, riesce finalmente ad avere un dialogo con il figlio. Ma in conclusione il direttore del carcere si rimangia la parola negando l\'abbreviazione della pena e vanificando così il sacrificio di Jesus.', 'michael23', '2021-01-21 13:05:48'),
(154, 360, 'Fa\' la cosa giusta', 'Fa\' la cosa giusta (Do the Right Thing) è un film del 1989 scritto, prodotto, diretto e interpretato da Spike Lee.\r\n\r\nInterpretato da un cast corale, il film si concentra sull\'esplosione delle tensioni razziali di un quartiere di Brooklyn nel corso di una giornata particolarmente calda. Considerato uno dei migliori film del regista afroamericano, alla sua uscita suscitò molte polemiche: per alcuni critici, infatti, il film istigava i giovani afroamericani dei quartieri popolari alla rivolta. Venne comunque candidato a due premi Oscar, per la migliore sceneggiatura originale e il miglior attore non protagonista (Danny Aiello).\r\n\r\nIl brano Fight the Power dei Public Enemy, parte della colonna sonora del film e dal testo fortemente critico verso elementi della società statunitense come il capitalismo, l\'influenza dei mass media e il fallimento dell\'integrazione razziale, ottenne un gran successo. Lee ne diresse anche il videoclip.\r\n\r\nNel 1999 è stato scelto per la conservazione nel National Film Registry della Biblioteca del Congresso degli Stati Uniti. Nel 2007, l\'American Film Institute l\'ha inserito al novantaseiesimo posto della classifica dei cento migliori film americani di tutti i tempi (nella classifica originaria del 1998 non era presente).', 'michael23', '2021-01-21 13:09:26'),
(155, 360, 'BlacKkKlansman', 'BlacKkKlansman, reso graficamente BLACKkKLANSMAN, è un film del 2018 diretto da Spike Lee.\r\n\r\nLa pellicola, adattamento cinematografico del libro Black Klansman scritto dall\'ex poliziotto Ron Stallworth, ha un cast che comprende John David Washington, Adam Driver e Topher Grace, ed è stato selezionato in concorso al Festival di Cannes 2018.\r\n\r\nIl film ha ricevuto sei candidature ai premi Oscar 2019 ed ha vinto l\'Oscar alla migliore sceneggiatura non originale.\r\nLa scena di Via col vento dei soldati confederati feriti e distesi sulle strade della Georgia, viene usata come pubblicità pubblica da Kennebrew Beauregard, il quale esprime una feroce denuncia contro le minoranze etniche e l\'integrazione razziale negli Stati Uniti.\r\n\r\nAll\'inizio degli anni settanta, Ron Stallworth è il primo afroamericano a diventare poliziotto a Colorado Springs. Inizialmente viene assegnato all\'archivio, dove deve affrontare il razzismo dei suoi colleghi. Richiede un trasferimento per essere un agente sotto copertura e viene inviato a infiltrarsi durante un comizio sui diritti civili dei neri tenuto da Kwame Ture. Durante il comizio conosce Patrice Dumas, presidente dell\'unione studentesca nera del Colorado College. Mentre Patrice riaccompagna Ture all\'hotel, viene fermata dall\'agente Andy Landers, un poliziotto razzista collega di Stallworth, che minaccia Ture e aggredisce Patrice.\r\n\r\nDopo il comizio, Stallworth viene riassegnato al dipartimento di intelligence. Mentre legge il giornale locale, nota un annuncio di reclutamento del Ku Klux Klan. Decide di chiamare il numero, fingendosi un uomo bianco, e parla con Walter Breachway, presidente del cantone di Colorado. Stallworth si fa aiutare dal suo collega Flip Zimmerman, che lo impersona per incontrare i membri del Ku Klux Klan di persona: Zimmerman partecipa agli incontri e incontra Walter Breachway, Felix Kedrickson, il membro più radicale del cantone, e un membro di nome Ivanhoe, che parla di un attacco imminente.\r\n\r\nZimmerman, nei panni di Stallworth, continua a coltivare l\'amicizia con il cantone locale. Per velocizzare l\'arrivo della sua tessera di membro, Stallworth chiama il quartier generale del Ku Klux Klan in Louisiana e riesce a parlare direttamente con David Duke, Gran maestro e presidente nazionale del Ku Klux Klan, con cui instaura un rapporto di fiducia. Kendrickson inizia a sospettare che qualcosa non vada e pretende che Zimmerman faccia il test della verità ebreo, ma Stallworth riesce a salvare il collega lanciando una pietra contro la casa dell\'uomo. Nel frattempo Stallworth inizia a uscire regolarmente con Patrice, celandole di essere un poliziotto. Dopo aver passato delle informazioni all\'Army Criminal Investigation Command sui membri del Klan, scopre che due affiliati sono agenti del NORAD.\r\n\r\nDuke visita Colorado Springs per la cerimonia di iniziazione di Stallworth al Klan e, nonostante le sue iniziali proteste, Stallworth gli viene assegnato come poliziotto di scorta. Dopo l\'iniziazione di Zimmerman, che impersona Stallworth, a membro del Klan, Connie, la moglie di Kendrickson, lascia la cerimonia per piazzare una bomba durante la marcia per i diritti civili organizzata dall\'unione studentesca nera del Colorado College. Stallworth riesce ad avvertire i colleghi e Connie, impaurita dalla notevole presenza di agenti nella zona, chiama Kendrickson, che le intima di passare al piano B. Connie si reca a casa di Patrice e tenta di nascondere il C-4 nella cassetta della posta, ma Patrice e la sua amica arrivano, interrompendo la sua azione.\r\n\r\nConnie decide quindi di piazzare la bomba sotto l\'auto di Patrice. Stallworth, arrivato sulla scena, vede Connie fuggire e tenta di fermarla, ma due agenti di pattuglia lo fermano e, pensando stia aggredendo Connie, lo arrestano e colpiscono con il manganello, nonostante questi sostenga di essere un agente sotto copertura. Kendrickson, Ivanhoe e Walker, amico di Kendrickson che aveva procurato l\'esplosivo e che, durante la cerimonia, aveva riconosciuto Zimmerman, arrivano a casa di Patrice e si fermano di fianco all\'auto della ragazza. Kendrickson attiva la bomba e i tre uomini vengono uccisi dall\'esplosione. Zimmerman arriva sul luogo e riesce a liberare Stallworth, mentre Connie viene arrestata.\r\n\r\nMentre festeggiano la vittoria in un bar, Stallworth, grazie a un microfono nascosto, riesce a incastrare Landers, che confessa la sua aggressione a Patrice; con la confessione registrata, Landers viene arrestato. Il capo della polizia Bridges si congratula con la squadra per il successo dell\'operazione, ma ordina il fermo delle operazioni per mantenere la cosa lontana dal pubblico. Mentre sta distruggendo tutti i documenti, Stallworth riceve una chiamata da Duke e, prima di riattaccare, il detective rivela di essere un uomo di colore. La sera Patrice e Stallworth discutono del loro futuro assieme, ma vengono interrotti da un colpo alla porta; dalla finestra vedono su una collina poco distante una croce in fiamme, circondata dai membri del Klan.\r\n\r\nIl film si conclude con immagini dei disordini dell\'agosto 2017 a Charlottesville, includendo anche delle scene con protagonisti suprematisti bianchi: David Duke che tiene un discorso, la contro-protesta dei bianchi, l\'attacco in auto fatto da James Alex Fields Jr. e le dichiarazioni del presidente Donald Trump dopo gli avvenimenti. Le ultime scene sono un memoriale a Heather Heyer, vittima dell\'attacco in auto, e una bandiera statunitense sottosopra, che lentamente sfuma verso il bianco e il nero.', 'michael23', '2021-01-21 13:10:58'),
(156, 361, 'What Could Possibly Go Wrong', 'What Could Possibly Go Wrong - Dominic Fike', 'dominic', '2021-01-21 13:46:16'),
(157, 361, 'After Hours', 'After Hours - The Weeknd', 'dominic', '2021-01-21 13:47:01'),
(158, 361, 'The Slow Rush', 'The Slow Rush - Tame Impala', 'dominic', '2021-01-21 13:49:06'),
(159, 361, 'Our Little Angel', 'Our Little Angel - Role Model', 'dominic', '2021-01-21 13:51:29'),
(160, 361, 'Chip Chrome & The Mono-Tones', 'Chip Chrome & The Mono-Tones - The Neighbourhood', 'dominic', '2021-01-21 13:54:41'),
(161, 361, 'Limbo', 'Limbo - Aminé', 'dominic', '2021-01-21 13:56:23');

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

CREATE TABLE `utente` (
  `id` int(10) UNSIGNED NOT NULL,
  `nomeUtente` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `estremiDocumento` varchar(9) NOT NULL,
  `numCartaCredito` varchar(20) DEFAULT NULL,
  `nomeCartaCredito` varchar(128) DEFAULT NULL,
  `scadenzaCartaCredito` date DEFAULT NULL,
  `CVVCartaCredito` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `utente`
--

INSERT INTO `utente` (`id`, `nomeUtente`, `password`, `email`, `telefono`, `estremiDocumento`, `numCartaCredito`, `nomeCartaCredito`, `scadenzaCartaCredito`, `CVVCartaCredito`) VALUES
(126, 'mario_rossi', '59195c6c541c8307f1da2d1e768d6f2280c984df217ad5f4c64c3542b04111a4', 'mariorossi@gmail.com', '12345678910', 'CA00000AA', NULL, NULL, NULL, NULL),
(127, 'michael23', '34550715062af006ac4fab288de67ecb44793c3a05c475227241535f6ef7a81b', 'michael23@gmail.com', '12345678910', 'CA00000AA', '1234567891234567', 'Michael Bolton', '1999-03-12', '123'),
(128, 'dominic', 'd16aed0a483a21d695fe5e210da62adec78c21631b5be5ff837e9fbedf2e6241', 'dominic@gmail.com', '12345678910', 'CA00000AA', '1234567891234567', 'Dominic Fike', '1978-05-10', '123'),
(129, 'gabe', '72831924521887e6638e686d6d004cd6cefe48168d2d4e2c40d29115b9c611b9', 'gabe@gmail.com', '12345678910', 'CA00000AA', NULL, NULL, NULL, NULL),
(130, 'luca', 'd70f47790f689414789eeff231703429c7f88a10210775906460edbf38589d90', 'luca@gmail.com', '12345678910', 'CA00000AA', NULL, NULL, NULL, NULL);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `argomenti`
--
ALTER TABLE `argomenti`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `argomentiblog`
--
ALTER TABLE `argomentiblog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idArgomenti` (`idArgomenti`),
  ADD KEY `idBlog` (`idBlog`);

--
-- Indici per le tabelle `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `autore` (`autore`);

--
-- Indici per le tabelle `commenti`
--
ALTER TABLE `commenti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idPost` (`idPost`),
  ADD KEY `autore` (`autore`);

--
-- Indici per le tabelle `immagini`
--
ALTER TABLE `immagini`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idPost` (`idPost`);

--
-- Indici per le tabelle `partecipanti`
--
ALTER TABLE `partecipanti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nomeUtente` (`nomeUtente`),
  ADD KEY `idBlog` (`idBlog`);

--
-- Indici per le tabelle `personalizzazione`
--
ALTER TABLE `personalizzazione`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idBlog` (`idBlog`);

--
-- Indici per le tabelle `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idBlog` (`idBlog`),
  ADD KEY `autore` (`autore`);

--
-- Indici per le tabelle `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomeUtente` (`nomeUtente`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `argomenti`
--
ALTER TABLE `argomenti`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=619;

--
-- AUTO_INCREMENT per la tabella `argomentiblog`
--
ALTER TABLE `argomentiblog`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=881;

--
-- AUTO_INCREMENT per la tabella `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=366;

--
-- AUTO_INCREMENT per la tabella `commenti`
--
ALTER TABLE `commenti`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT per la tabella `immagini`
--
ALTER TABLE `immagini`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT per la tabella `partecipanti`
--
ALTER TABLE `partecipanti`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=443;

--
-- AUTO_INCREMENT per la tabella `personalizzazione`
--
ALTER TABLE `personalizzazione`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=356;

--
-- AUTO_INCREMENT per la tabella `post`
--
ALTER TABLE `post`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=168;

--
-- AUTO_INCREMENT per la tabella `utente`
--
ALTER TABLE `utente`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `argomentiblog`
--
ALTER TABLE `argomentiblog`
  ADD CONSTRAINT `argomentiblog_ibfk_1` FOREIGN KEY (`idArgomenti`) REFERENCES `argomenti` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `argomentiblog_ibfk_2` FOREIGN KEY (`idBlog`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `blog`
--
ALTER TABLE `blog`
  ADD CONSTRAINT `blog_ibfk_1` FOREIGN KEY (`autore`) REFERENCES `utente` (`nomeUtente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `commenti`
--
ALTER TABLE `commenti`
  ADD CONSTRAINT `commenti_ibfk_1` FOREIGN KEY (`idPost`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commenti_ibfk_2` FOREIGN KEY (`autore`) REFERENCES `utente` (`nomeUtente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `immagini`
--
ALTER TABLE `immagini`
  ADD CONSTRAINT `immagini_ibfk_1` FOREIGN KEY (`idPost`) REFERENCES `post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `partecipanti`
--
ALTER TABLE `partecipanti`
  ADD CONSTRAINT `partecipanti_ibfk_1` FOREIGN KEY (`nomeUtente`) REFERENCES `utente` (`nomeUtente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `partecipanti_ibfk_2` FOREIGN KEY (`idBlog`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `personalizzazione`
--
ALTER TABLE `personalizzazione`
  ADD CONSTRAINT `personalizzazione_ibfk_1` FOREIGN KEY (`idBlog`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`idBlog`) REFERENCES `blog` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`autore`) REFERENCES `utente` (`nomeUtente`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
