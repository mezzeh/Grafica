-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 15, 2025 at 11:29 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistema_studio`
--

-- --------------------------------------------------------

--
-- Table structure for table `argomenti`
--

CREATE TABLE `argomenti` (
  `id` int(11) NOT NULL,
  `esame_id` int(11) NOT NULL,
  `titolo` varchar(100) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `livello_importanza` int(11) DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `argomenti`
--

INSERT INTO `argomenti` (`id`, `esame_id`, `titolo`, `descrizione`, `livello_importanza`) VALUES
(1, 1, 'Introduzione a Java', 'Concetti base del linguaggio Java e ambiente di sviluppo', 5),
(2, 1, 'Classi e Oggetti', 'Programmazione orientata agli oggetti in Java', 5),
(3, 1, 'Array e Collections', 'Strutture dati fondamentali in Java', 4),
(4, 1, 'Ereditarietà e Polimorfismo', 'Concetti avanzati di OOP', 4),
(5, 1, 'Eccezioni', 'Gestione degli errori in Java', 3),
(6, 2, 'Complessità Algoritmica', 'Notazione O e analisi della complessità', 5),
(7, 2, 'Algoritmi di Ordinamento', 'QuickSort, MergeSort, HeapSort, etc.', 5),
(8, 2, 'Alberi e Grafi', 'Rappresentazione e algoritmi su alberi e grafi', 4),
(9, 2, 'Liste e Code', 'Implementazione e operazioni su liste, pile e code', 4),
(10, 3, 'Modello Relazionale', 'Concetti fondamentali delle basi di dati relazionali', 5),
(11, 3, 'SQL', 'Linguaggio di interrogazione dei database', 5),
(12, 3, 'Normalizzazione', 'Forme normali e progettazione database', 4),
(13, 3, 'Transazioni', 'Proprietà ACID e gestione delle transazioni', 3),
(14, 4, 'Processi e Thread', 'Concetti di processo, thread e concorrenza', 5),
(15, 4, 'Scheduling', 'Algoritmi di scheduling della CPU', 4),
(16, 4, 'Gestione Memoria', 'Tecniche di gestione della memoria', 4),
(17, 4, 'File System', 'Organizzazione e gestione dei file', 3),
(18, 5, 'Limiti e Continuità', 'Definizioni e teoremi sui limiti', 5),
(19, 5, 'Derivate', 'Calcolo differenziale e applicazioni', 5),
(20, 5, 'Integrali', 'Calcolo integrale e applicazioni', 5),
(21, 5, 'Serie Numeriche', 'Convergenza e divergenza di serie', 4),
(22, 6, 'Vettori', 'Operazioni e proprietà dei vettori', 5),
(23, 6, 'Matrici', 'Algebra delle matrici', 5),
(24, 6, 'Sistemi Lineari', 'Risoluzione di sistemi di equazioni lineari', 4),
(25, 6, 'Spazi Vettoriali', 'Basi, dimensione e trasformazioni lineari', 4),
(26, 9, 'Cinematica', 'Studio del moto dei corpi', 5),
(27, 9, 'Dinamica', 'Leggi di Newton e applicazioni', 5),
(28, 9, 'Energia e Lavoro', 'Conservazione dell\'energia e teorema dell\'energia cinetica', 4),
(29, 9, 'Momento Angolare', 'Conservazione del momento angolare', 3),
(30, 10, 'Campo Elettrico', 'Legge di Coulomb e campo elettrico', 5),
(31, 10, 'Campo Magnetico', 'Forza di Lorentz e campo magnetico', 5),
(32, 10, 'Equazioni di Maxwell', 'Formulazione matematica dell\'elettromagnetismo', 4),
(33, 10, 'Onde Elettromagnetiche', 'Propagazione delle onde EM', 3);

-- --------------------------------------------------------

--
-- Table structure for table `commenti`
--

CREATE TABLE `commenti` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tipo_elemento` varchar(50) NOT NULL,
  `elemento_id` int(11) NOT NULL,
  `testo` text NOT NULL,
  `data_creazione` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_modifica` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `esami`
--

CREATE TABLE `esami` (
  `id` int(11) NOT NULL,
  `piano_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `codice` varchar(20) DEFAULT NULL,
  `crediti` int(11) DEFAULT 6,
  `descrizione` text DEFAULT NULL,
  `anno` int(11) DEFAULT NULL,
  `semestre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `esami`
--

INSERT INTO `esami` (`id`, `piano_id`, `nome`, `codice`, `crediti`, `descrizione`, `anno`, `semestre`) VALUES
(1, 1, 'Programmazione I', 'INF001', 12, 'Introduzione alla programmazione con Java', 1, 1),
(2, 1, 'Algoritmi e Strutture Dati', 'INF002', 9, 'Algoritmi fondamentali e strutture dati', 1, 2),
(3, 1, 'Basi di Dati', 'INF003', 9, 'Progettazione e implementazione di database relazionali', 2, 1),
(4, 1, 'Sistemi Operativi', 'INF004', 6, 'Concetti fondamentali dei sistemi operativi', 2, 2),
(5, 2, 'Analisi Matematica I', 'MAT001', 12, 'Limiti, derivate e integrali', 1, 1),
(6, 2, 'Algebra Lineare', 'MAT002', 9, 'Vettori, matrici e sistemi lineari', 1, 2),
(7, 2, 'Geometria', 'MAT003', 9, 'Geometria analitica e differenziale', 2, 1),
(8, 2, 'Analisi Numerica', 'MAT004', 6, 'Metodi numerici per problemi matematici', 2, 2),
(9, 3, 'Meccanica', 'FIS001', 12, 'Principi della meccanica classica', 1, 1),
(10, 3, 'Elettromagnetismo', 'FIS002', 9, 'Teoria elettromagnetica di Maxwell', 1, 2),
(11, 3, 'Termodinamica', 'FIS003', 9, 'Principi della termodinamica', 2, 1),
(12, 4, 'Reti di Calcolatori', 'ING001', 9, 'Protocolli e architetture di rete', 1, 1),
(13, 4, 'Ingegneria del Software', 'ING002', 9, 'Metodologie di sviluppo software', 1, 2),
(14, 4, 'Intelligenza Artificiale', 'ING003', 6, 'Algoritmi e tecniche di AI', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `esercizi`
--

CREATE TABLE `esercizi` (
  `id` int(11) NOT NULL,
  `sottoargomento_id` int(11) NOT NULL,
  `titolo` varchar(100) NOT NULL,
  `testo` text NOT NULL,
  `soluzione` text DEFAULT NULL,
  `difficolta` int(11) DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `esercizi`
--

INSERT INTO `esercizi` (`id`, `sottoargomento_id`, `titolo`, `testo`, `soluzione`, `difficolta`) VALUES
(1, 1, 'Dichiarazione Variabili', 'Scrivere un programma Java che dichiari e inizializzi variabili di tutti i tipi primitivi.', 'public class TipiPrimitivi {\n    public static void main(String[] args) {\n        byte b = 10;\n        short s = 100;\n        int i = 1000;\n        long l = 10000L;\n        float f = 3.14f;\n        double d = 3.14159;\n        char c = \'A\';\n        boolean bool = true;\n        \n        System.out.println(\"byte: \" + b);\n        System.out.println(\"short: \" + s);\n        System.out.println(\"int: \" + i);\n        System.out.println(\"long: \" + l);\n        System.out.println(\"float: \" + f);\n        System.out.println(\"double: \" + d);\n        System.out.println(\"char: \" + c);\n        System.out.println(\"boolean: \" + bool);\n    }\n}', 1),
(2, 3, 'Ciclo For', 'Scrivere un programma Java che utilizzi un ciclo for per stampare i numeri da 1 a 10.', 'public class CicloFor {\n    public static void main(String[] args) {\n        for(int i = 1; i <= 10; i++) {\n            System.out.println(i);\n        }\n    }\n}', 1),
(3, 3, 'Ciclo While', 'Scrivere un programma Java che utilizzi un ciclo while per calcolare la somma dei numeri da 1 a 100.', 'public class SommaWhile {\n    public static void main(String[] args) {\n        int sum = 0;\n        int i = 1;\n        while(i <= 100) {\n            sum += i;\n            i++;\n        }\n        System.out.println(\"La somma è: \" + sum);\n    }\n}', 2),
(4, 5, 'Definizione Classe', 'Creare una classe Studente con attributi nome, cognome ed età, e un metodo per visualizzare le informazioni.', 'public class Studente {\n    private String nome;\n    private String cognome;\n    private int eta;\n    \n    public Studente(String nome, String cognome, int eta) {\n        this.nome = nome;\n        this.cognome = cognome;\n        this.eta = eta;\n    }\n    \n    public void visualizzaInfo() {\n        System.out.println(\"Nome: \" + nome);\n        System.out.println(\"Cognome: \" + cognome);\n        System.out.println(\"Età: \" + eta);\n    }\n}', 2),
(5, 9, 'Array Somma', 'Scrivere un metodo che calcoli la somma degli elementi di un array di interi.', 'public static int sommaArray(int[] array) {\n    int somma = 0;\n    for(int elemento : array) {\n        somma += elemento;\n    }\n    return somma;\n}', 1),
(6, 9, 'Array Max Min', 'Trovare il valore massimo e minimo in un array di interi.', 'public static void trovaMaxMin(int[] array) {\n    if(array.length == 0) {\n        System.out.println(\"Array vuoto\");\n        return;\n    }\n    \n    int max = array[0];\n    int min = array[0];\n    \n    for(int i = 1; i < array.length; i++) {\n        if(array[i] > max) {\n            max = array[i];\n        }\n        if(array[i] < min) {\n            min = array[i];\n        }\n    }\n    \n    System.out.println(\"Massimo: \" + max);\n    System.out.println(\"Minimo: \" + min);\n}', 2),
(7, 17, 'QuickSort Implementazione', 'Implementare l\'algoritmo QuickSort per ordinare un array di interi.', 'public static void quickSort(int[] arr, int low, int high) {\n    if (low < high) {\n        int pi = partition(arr, low, high);\n        quickSort(arr, low, pi - 1);\n        quickSort(arr, pi + 1, high);\n    }\n}\n\nprivate static int partition(int[] arr, int low, int high) {\n    int pivot = arr[high];\n    int i = (low - 1);\n    for (int j = low; j < high; j++) {\n        if (arr[j] <= pivot) {\n            i++;\n            int temp = arr[i];\n            arr[i] = arr[j];\n            arr[j] = temp;\n        }\n    }\n    int temp = arr[i + 1];\n    arr[i + 1] = arr[high];\n    arr[high] = temp;\n    return i + 1;\n}', 3),
(8, 29, 'Limite Funzione', 'Calcolare il limite di (x^2-1)/(x-1) per x che tende a 1.', 'Applichiamo la regola di de l\'Hôpital o scomponiamo:\n(x^2-1)/(x-1) = ((x-1)(x+1))/(x-1) = x+1 per x ≠ 1\n\nQuindi il limite per x→1 è:\nlim(x→1) (x^2-1)/(x-1) = lim(x→1) (x+1) = 1+1 = 2', 2),
(9, 30, 'Continuità Funzione', 'Studiare la continuità della funzione f(x) = (x^2-9)/(x-3) nel punto x = 3.', 'La funzione f(x) = (x^2-9)/(x-3) = ((x-3)(x+3))/(x-3) = x+3 per x ≠ 3\n\nQuindi f(x) = x+3 per x ≠ 3, che è una funzione continua in tutti i punti tranne x = 3 dove non è definita.\n\nPer x = 3, il limite della funzione è:\nlim(x→3) f(x) = lim(x→3) (x+3) = 3+3 = 6\n\nPossiamo ridefinire la funzione come:\nf(x) = x+3 se x ≠ 3\nf(3) = 6\n\nCon questa definizione, la funzione diventa continua anche in x = 3.', 2),
(10, 33, 'Derivata Funzione', 'Calcolare la derivata della funzione f(x) = x^3 + 2x^2 - 5x + 1.', 'Applichiamo le regole di derivazione:\nf\'(x) = 3x^2 + 4x - 5', 1),
(11, 34, 'Derivata Composta', 'Calcolare la derivata della funzione f(x) = sin(x^2).', 'Applichiamo la regola della catena:\nf\'(x) = cos(x^2) · 2x = 2x · cos(x^2)', 2),
(12, 37, 'Integrale Base', 'Calcolare l\'integrale indefinito di f(x) = 2x + 3.', '∫(2x + 3)dx = x^2 + 3x + C', 1),
(13, 38, 'Integrazione per Parti', 'Calcolare l\'integrale indefinito di f(x) = x·e^x.', 'Usiamo il metodo di integrazione per parti: ∫u·dv = u·v - ∫v·du\nSia u = x e dv = e^x dx, allora du = dx e v = e^x\n\n∫x·e^x dx = x·e^x - ∫e^x dx = x·e^x - e^x + C = e^x(x-1) + C', 3),
(14, 39, 'Area sotto curva', 'Calcolare l\'area della regione limitata dalla curva y = x^2 e dall\'asse x tra x = 0 e x = 2.', 'L\'area è data dall\'integrale definito:\n∫(0→2) x^2 dx = [x^3/3](0→2) = 2^3/3 - 0 = 8/3 ≈ 2.67', 2);

-- --------------------------------------------------------

--
-- Table structure for table `esercizio_correlato`
--

CREATE TABLE `esercizio_correlato` (
  `id` int(11) NOT NULL,
  `esercizio_id` int(11) NOT NULL,
  `esercizio_correlato_id` int(11) NOT NULL,
  `tipo_relazione` varchar(50) NOT NULL,
  `data_creazione` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `esercizio_correlato`
--

INSERT INTO `esercizio_correlato` (`id`, `esercizio_id`, `esercizio_correlato_id`, `tipo_relazione`, `data_creazione`) VALUES
(1, 1, 2, 'prerequisito', '2025-03-15 10:27:57'),
(2, 1, 3, 'correlato', '2025-03-15 10:27:57'),
(3, 2, 3, 'correlato', '2025-03-15 10:27:57'),
(4, 3, 4, 'prerequisito', '2025-03-15 10:27:57'),
(5, 4, 5, 'successivo', '2025-03-15 10:27:57'),
(6, 5, 6, 'correlato', '2025-03-15 10:27:57'),
(7, 7, 8, 'successivo', '2025-03-15 10:27:57'),
(8, 8, 9, 'correlato', '2025-03-15 10:27:57'),
(9, 10, 11, 'correlato', '2025-03-15 10:27:57'),
(10, 12, 13, 'prerequisito', '2025-03-15 10:27:57'),
(11, 13, 14, 'correlato', '2025-03-15 10:27:57');

-- --------------------------------------------------------

--
-- Table structure for table `formula_argomento`
--

CREATE TABLE `formula_argomento` (
  `formula_id` int(11) NOT NULL,
  `argomento_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `formule`
--

CREATE TABLE `formule` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `espressione` text NOT NULL,
  `descrizione` text DEFAULT NULL,
  `immagine` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `piani_di_studio`
--

CREATE TABLE `piani_di_studio` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `data_creazione` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `visibility` varchar(20) NOT NULL DEFAULT 'private'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `piani_di_studio`
--

INSERT INTO `piani_di_studio` (`id`, `nome`, `descrizione`, `data_creazione`, `user_id`, `visibility`) VALUES
(1, 'Piano Informatica Triennale', 'Piano di studi completo per il corso di laurea in Informatica', '2025-03-15 10:23:23', 2, 'public'),
(2, 'Piano Matematica', 'Piano per il corso di laurea in Matematica', '2025-03-15 10:23:23', 3, 'public'),
(3, 'Piano Fisica', 'Piano per il corso di laurea in Fisica', '2025-03-15 10:23:23', 5, 'public'),
(4, 'Piano Ingegneria Informatica', 'Piano di studi per ingegneria informatica magistrale', '2025-03-15 10:23:23', 4, 'public');

-- --------------------------------------------------------

--
-- Table structure for table `requisiti`
--

CREATE TABLE `requisiti` (
  `id` int(11) NOT NULL,
  `esercizio_id` int(11) NOT NULL,
  `descrizione` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requisiti`
--

INSERT INTO `requisiti` (`id`, `esercizio_id`, `descrizione`) VALUES
(1, 1, 'Conoscenza base del linguaggio Java'),
(2, 1, 'Comprensione dei tipi di dati primitivi'),
(3, 2, 'Conoscenza delle strutture di controllo'),
(4, 2, 'Comprensione delle variabili e operatori'),
(5, 3, 'Conoscenza delle strutture iterative'),
(6, 3, 'Comprensione del concetto di accumulazione'),
(7, 4, 'Conoscenza dei concetti di classe e oggetto'),
(8, 4, 'Comprensione dell\'incapsulamento'),
(9, 5, 'Conoscenza degli array'),
(10, 5, 'Comprensione dei cicli for'),
(11, 6, 'Conoscenza degli algoritmi di confronto'),
(12, 7, 'Conoscenza approfondita degli algoritmi di ordinamento'),
(13, 7, 'Comprensione della ricorsione'),
(14, 8, 'Conoscenza dei limiti di funzioni'),
(15, 9, 'Comprensione del concetto di continuità'),
(16, 10, 'Conoscenza delle regole di derivazione di base'),
(17, 11, 'Conoscenza della regola della catena per derivate'),
(18, 12, 'Conoscenza dell\'integrazione indefinita'),
(19, 13, 'Comprensione dell\'integrazione per parti'),
(20, 14, 'Comprensione dell\'integrazione definita');

-- --------------------------------------------------------

--
-- Table structure for table `requisito_argomento`
--

CREATE TABLE `requisito_argomento` (
  `requisito_id` int(11) NOT NULL,
  `argomento_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requisito_argomento`
--

INSERT INTO `requisito_argomento` (`requisito_id`, `argomento_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 2),
(8, 2),
(9, 3),
(10, 1),
(11, 7),
(12, 7),
(13, 1),
(14, 19),
(15, 19),
(16, 20),
(17, 20),
(18, 21),
(19, 21),
(20, 21);

-- --------------------------------------------------------

--
-- Table structure for table `sottoargomenti`
--

CREATE TABLE `sottoargomenti` (
  `id` int(11) NOT NULL,
  `argomento_id` int(11) NOT NULL,
  `titolo` varchar(100) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `livello_profondita` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sottoargomenti`
--

INSERT INTO `sottoargomenti` (`id`, `argomento_id`, `titolo`, `descrizione`, `livello_profondita`) VALUES
(1, 1, 'Variabili e Tipi', 'Dichiarazione di variabili e tipi primitivi in Java', 1),
(2, 1, 'Operatori', 'Operatori aritmetici, relazionali e logici', 1),
(3, 1, 'Strutture di Controllo', 'If-else, switch, cicli for e while', 2),
(4, 1, 'Input/Output', 'Lettura e scrittura da console e file', 2),
(5, 2, 'Definizione di Classe', 'Creazione di classi e istanziazione di oggetti', 1),
(6, 2, 'Attributi e Metodi', 'Membri di una classe e loro visibilità', 1),
(7, 2, 'Costruttori', 'Definizione e overloading dei costruttori', 2),
(8, 2, 'Incapsulamento', 'Principi di information hiding', 2),
(9, 3, 'Array Monodimensionali', 'Dichiarazione e manipolazione di array', 1),
(10, 3, 'Array Multidimensionali', 'Matrici e array di array', 2),
(11, 3, 'ArrayList', 'Utilizzo delle liste dinamiche', 2),
(12, 3, 'Set e Map', 'Insiemi e dizionari in Java', 3),
(13, 6, 'Notazione O-grande', 'Definizione e proprietà della notazione asintotica', 1),
(14, 6, 'Analisi di Algoritmi Iterativi', 'Tecniche per calcolare la complessità di cicli', 2),
(15, 6, 'Analisi di Algoritmi Ricorsivi', 'Risoluzione di ricorrenze', 3),
(16, 6, 'Classi di Complessità', 'P, NP e problemi intrattabili', 3),
(17, 7, 'BubbleSort', 'Algoritmo di ordinamento a bolle', 1),
(18, 7, 'QuickSort', 'Algoritmo di ordinamento veloce', 2),
(19, 7, 'MergeSort', 'Algoritmo di ordinamento per fusione', 2),
(20, 7, 'HeapSort', 'Algoritmo di ordinamento basato su heap', 3),
(21, 10, 'Modello E-R', 'Progettazione concettuale con diagrammi E-R', 1),
(22, 10, 'Tabelle e Relazioni', 'Struttura fisica del database relazionale', 1),
(23, 10, 'Vincoli di Integrità', 'Chiavi primarie, foreign key e altri vincoli', 2),
(24, 10, 'Query di Base', 'Interrogazioni SQL fondamentali', 2),
(25, 11, 'SELECT', 'Interrogazioni di selezione dati', 1),
(26, 11, 'JOIN', 'Congiunzioni tra tabelle', 2),
(27, 11, 'GROUP BY', 'Aggregazione di dati', 2),
(28, 11, 'Subquery', 'Query annidate', 3),
(29, 19, 'Funzioni e Limiti', 'Concetto di limite e teoremi fondamentali', 1),
(30, 19, 'Continuità', 'Funzioni continue e teoremi', 1),
(31, 19, 'Limiti Notevoli', 'Limiti fondamentali e loro applicazioni', 2),
(32, 19, 'Asintoti', 'Ricerca degli asintoti di una funzione', 2),
(33, 20, 'Definizione di Derivata', 'Rapporto incrementale e derivata', 1),
(34, 20, 'Regole di Derivazione', 'Derivate di funzioni elementari e composte', 1),
(35, 20, 'Derivate Successive', 'Derivate di ordine superiore', 2),
(36, 20, 'Applicazioni delle Derivate', 'Studio di funzione', 2),
(37, 21, 'Integrale Indefinito', 'Primitive e proprietà degli integrali', 1),
(38, 21, 'Metodi di Integrazione', 'Sostituzione, parti, fratti semplici', 2),
(39, 21, 'Integrale Definito', 'Teorema fondamentale del calcolo', 2),
(40, 21, 'Applicazioni degli Integrali', 'Calcolo di aree e volumi', 3);

-- --------------------------------------------------------

--
-- Table structure for table `sottoargomento_argomento_prerequisito`
--

CREATE TABLE `sottoargomento_argomento_prerequisito` (
  `sottoargomento_id` int(11) NOT NULL,
  `argomento_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sottoargomento_requisito`
--

CREATE TABLE `sottoargomento_requisito` (
  `id` int(11) NOT NULL,
  `sottoargomento_id` int(11) NOT NULL,
  `requisito_tipo` varchar(50) NOT NULL,
  `requisito_id` int(11) NOT NULL,
  `data_creazione` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sottoargomento_sottoargomento_prerequisito`
--

CREATE TABLE `sottoargomento_sottoargomento_prerequisito` (
  `sottoargomento_id` int(11) NOT NULL,
  `prerequisito_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$GFyFe.FrTjJZfdb98WY5w.DOfLeeK4Gdfa2Wgi93BO5jRoW2zgBfG', 'admin@example.com', 'admin', '2025-03-15 10:23:23'),
(2, 'mario_rossi', '$2y$10$Q3i5LI1cz0N8fg9B1ChumeUdZbVxkrCBIsuFoI8vkzoA0e7ue5q5S', 'mario.rossi@gmail.com', 'user', '2025-03-15 10:23:23'),
(3, 'laura_bianchi', '$2y$10$nzBf0bCklAceusPLqCU/ruifsi6vNt/zfqOBpoHtVXe2yqoTdzgcq', 'laura.bianchi@gmail.com', 'user', '2025-03-15 10:23:23'),
(4, 'marco_verdi', '$2y$10$W0xFNRqlP.lfNLP6dlPdmuOrdgLW5F1mp5YPeY.BM8UswjX3bR006', 'marco.verdi@gmail.com', 'user', '2025-03-15 10:23:23'),
(5, 'giulia_neri', '$2y$10$EkVdb4wy7OwTKjDoA8LmEOKII4SR9wfON7J/LBnXK251L.x4XJqda', 'giulia.neri@gmail.com', 'user', '2025-03-15 10:23:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `argomenti`
--
ALTER TABLE `argomenti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_argomenti_esame` (`esame_id`);

--
-- Indexes for table `commenti`
--
ALTER TABLE `commenti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_commenti_user` (`user_id`),
  ADD KEY `idx_commenti_elemento` (`tipo_elemento`,`elemento_id`);

--
-- Indexes for table `esami`
--
ALTER TABLE `esami`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_esami_piano` (`piano_id`);

--
-- Indexes for table `esercizi`
--
ALTER TABLE `esercizi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_esercizi_sottoargomento` (`sottoargomento_id`);

--
-- Indexes for table `esercizio_correlato`
--
ALTER TABLE `esercizio_correlato`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_esercizio` (`esercizio_id`),
  ADD KEY `idx_esercizio_correlato` (`esercizio_correlato_id`);

--
-- Indexes for table `formula_argomento`
--
ALTER TABLE `formula_argomento`
  ADD PRIMARY KEY (`formula_id`,`argomento_id`),
  ADD KEY `argomento_id` (`argomento_id`);

--
-- Indexes for table `formule`
--
ALTER TABLE `formule`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `piani_di_studio`
--
ALTER TABLE `piani_di_studio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_piani_user` (`user_id`),
  ADD KEY `idx_piani_visibility` (`visibility`);

--
-- Indexes for table `requisiti`
--
ALTER TABLE `requisiti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_requisiti_esercizio` (`esercizio_id`);

--
-- Indexes for table `requisito_argomento`
--
ALTER TABLE `requisito_argomento`
  ADD PRIMARY KEY (`requisito_id`,`argomento_id`),
  ADD KEY `argomento_id` (`argomento_id`);

--
-- Indexes for table `sottoargomenti`
--
ALTER TABLE `sottoargomenti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sottoargomenti_argomento` (`argomento_id`);

--
-- Indexes for table `sottoargomento_argomento_prerequisito`
--
ALTER TABLE `sottoargomento_argomento_prerequisito`
  ADD PRIMARY KEY (`sottoargomento_id`,`argomento_id`),
  ADD KEY `idx_argomento` (`argomento_id`);

--
-- Indexes for table `sottoargomento_requisito`
--
ALTER TABLE `sottoargomento_requisito`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sottoargomento` (`sottoargomento_id`);

--
-- Indexes for table `sottoargomento_sottoargomento_prerequisito`
--
ALTER TABLE `sottoargomento_sottoargomento_prerequisito`
  ADD PRIMARY KEY (`sottoargomento_id`,`prerequisito_id`),
  ADD KEY `idx_prerequisito` (`prerequisito_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `argomenti`
--
ALTER TABLE `argomenti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `commenti`
--
ALTER TABLE `commenti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `esami`
--
ALTER TABLE `esami`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `esercizi`
--
ALTER TABLE `esercizi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `esercizio_correlato`
--
ALTER TABLE `esercizio_correlato`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `formule`
--
ALTER TABLE `formule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `piani_di_studio`
--
ALTER TABLE `piani_di_studio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `requisiti`
--
ALTER TABLE `requisiti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `sottoargomenti`
--
ALTER TABLE `sottoargomenti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `sottoargomento_requisito`
--
ALTER TABLE `sottoargomento_requisito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `argomenti`
--
ALTER TABLE `argomenti`
  ADD CONSTRAINT `argomenti_ibfk_1` FOREIGN KEY (`esame_id`) REFERENCES `esami` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `commenti`
--
ALTER TABLE `commenti`
  ADD CONSTRAINT `commenti_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `esami`
--
ALTER TABLE `esami`
  ADD CONSTRAINT `esami_ibfk_1` FOREIGN KEY (`piano_id`) REFERENCES `piani_di_studio` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `esercizi`
--
ALTER TABLE `esercizi`
  ADD CONSTRAINT `esercizi_ibfk_1` FOREIGN KEY (`sottoargomento_id`) REFERENCES `sottoargomenti` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `esercizio_correlato`
--
ALTER TABLE `esercizio_correlato`
  ADD CONSTRAINT `esercizio_correlato_ibfk_1` FOREIGN KEY (`esercizio_id`) REFERENCES `esercizi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `esercizio_correlato_ibfk_2` FOREIGN KEY (`esercizio_correlato_id`) REFERENCES `esercizi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `formula_argomento`
--
ALTER TABLE `formula_argomento`
  ADD CONSTRAINT `formula_argomento_ibfk_1` FOREIGN KEY (`formula_id`) REFERENCES `formule` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `formula_argomento_ibfk_2` FOREIGN KEY (`argomento_id`) REFERENCES `argomenti` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `piani_di_studio`
--
ALTER TABLE `piani_di_studio`
  ADD CONSTRAINT `piani_di_studio_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `requisiti`
--
ALTER TABLE `requisiti`
  ADD CONSTRAINT `requisiti_ibfk_1` FOREIGN KEY (`esercizio_id`) REFERENCES `esercizi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `requisito_argomento`
--
ALTER TABLE `requisito_argomento`
  ADD CONSTRAINT `requisito_argomento_ibfk_1` FOREIGN KEY (`requisito_id`) REFERENCES `requisiti` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requisito_argomento_ibfk_2` FOREIGN KEY (`argomento_id`) REFERENCES `argomenti` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sottoargomenti`
--
ALTER TABLE `sottoargomenti`
  ADD CONSTRAINT `sottoargomenti_ibfk_1` FOREIGN KEY (`argomento_id`) REFERENCES `argomenti` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sottoargomento_argomento_prerequisito`
--
ALTER TABLE `sottoargomento_argomento_prerequisito`
  ADD CONSTRAINT `sottoargomento_argomento_prerequisito_ibfk_1` FOREIGN KEY (`sottoargomento_id`) REFERENCES `sottoargomenti` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sottoargomento_argomento_prerequisito_ibfk_2` FOREIGN KEY (`argomento_id`) REFERENCES `argomenti` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sottoargomento_requisito`
--
ALTER TABLE `sottoargomento_requisito`
  ADD CONSTRAINT `sottoargomento_requisito_ibfk_1` FOREIGN KEY (`sottoargomento_id`) REFERENCES `sottoargomenti` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sottoargomento_sottoargomento_prerequisito`
--
ALTER TABLE `sottoargomento_sottoargomento_prerequisito`
  ADD CONSTRAINT `sottoargomento_sottoargomento_prerequisito_ibfk_1` FOREIGN KEY (`sottoargomento_id`) REFERENCES `sottoargomenti` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sottoargomento_sottoargomento_prerequisito_ibfk_2` FOREIGN KEY (`prerequisito_id`) REFERENCES `sottoargomenti` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
