-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: jtocs
-- Generation Time: Jan 12, 2022 at 04:52 PM
-- Server version: 5.5.68-MariaDB
-- PHP Version: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


-- --------------------------------------------------------

--
-- Table structure for table `sdStopword`
--

CREATE TABLE `sdStopword` (
  `word` char(32) CHARACTER SET utf8 NOT NULL,
  `lang` char(2) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sdStopword`
--

INSERT INTO `sdStopword` (`word`, `lang`) VALUES
('a', 'en'),
('about', 'en'),
('abst', 'en'),
('according', 'en'),
('accordingly', 'en'),
('actually', 'en'),
('added', 'en'),
('adj', 'en'),
('affected', 'en'),
('affecting', 'en'),
('affects', 'en'),
('after', 'en'),
('afterwards', 'en'),
('again', 'en'),
('ah', 'en'),
('all', 'en'),
('almost', 'en'),
('along', 'en'),
('already', 'en'),
('also', 'en'),
('although', 'en'),
('always', 'en'),
('am', 'en'),
('among', 'en'),
('amongst', 'en'),
('an', 'en'),
('and', 'en'),
('another', 'en'),
('any', 'en'),
('anyhow', 'en'),
('anymore', 'en'),
('anyone', 'en'),
('anything', 'en'),
('anywhere', 'en'),
('apparently', 'en'),
('approximately', 'en'),
('are', 'en'),
('aren', 'en'),
('arent', 'en'),
('arise', 'en'),
('around', 'en'),
('as', 'en'),
('aside', 'en'),
('at', 'en'),
('auth', 'en'),
('away', 'en'),
('b', 'en'),
('back', 'en'),
('be', 'en'),
('became', 'en'),
('because', 'en'),
('become', 'en'),
('becomes', 'en'),
('becoming', 'en'),
('been', 'en'),
('before', 'en'),
('beforehand', 'en'),
('beginning', 'en'),
('beginnings', 'en'),
('begins', 'en'),
('being', 'en'),
('below', 'en'),
('beside', 'en'),
('besides', 'en'),
('biol', 'en'),
('both', 'en'),
('briefly', 'en'),
('but', 'en'),
('by', 'en'),
('c', 'en'),
('ca', 'en'),
('came', 'en'),
('can', 'en'),
('cannot', 'en'),
('cant', 'en'),
('certainly', 'en'),
('co', 'en'),
('co.', 'en'),
('come', 'en'),
('contains', 'en'),
('could', 'en'),
('couldnt', 'en'),
('d', 'en'),
('date', 'en'),
('did', 'en'),
('didnt', 'en'),
('do', 'en'),
('does', 'en'),
('doesnt', 'en'),
('doing', 'en'),
('done', 'en'),
('dont', 'en'),
('down', 'en'),
('due', 'en'),
('during', 'en'),
('e', 'en'),
('each', 'en'),
('ed', 'en'),
('eg', 'en'),
('eighty', 'en'),
('either', 'en'),
('else', 'en'),
('elsewhere', 'en'),
('end', 'en'),
('ending', 'en'),
('especially', 'en'),
('et-al', 'en'),
('etc', 'en'),
('ever', 'en'),
('every', 'en'),
('everyone', 'en'),
('everything', 'en'),
('everywhere', 'en'),
('f', 'en'),
('few', 'en'),
('ff', 'en'),
('fix', 'en'),
('followed', 'en'),
('following', 'en'),
('for', 'en'),
('formerly', 'en'),
('found', 'en'),
('from', 'en'),
('g', 'en'),
('gave', 'en'),
('get', 'en'),
('gets', 'en'),
('give', 'en'),
('given', 'en'),
('giving', 'en'),
('go', 'en'),
('goes', 'en'),
('gone', 'en'),
('got', 'en'),
('h', 'en'),
('had', 'en'),
('hardly', 'en'),
('has', 'en'),
('hasnt', 'en'),
('have', 'en'),
('havent', 'en'),
('having', 'en'),
('he', 'en'),
('hed', 'en'),
('hence', 'en'),
('her', 'en'),
('here', 'en'),
('hereafter', 'en'),
('hereby', 'en'),
('herein', 'en'),
('heres', 'en'),
('hereupon', 'en'),
('hers', 'en'),
('herself', 'en'),
('hes', 'en'),
('hid', 'en'),
('him', 'en'),
('himself', 'en'),
('his', 'en'),
('how', 'en'),
('however', 'en'),
('i', 'en'),
('id', 'en'),
('ie', 'en'),
('if', 'en'),
('ill', 'en'),
('im', 'en'),
('immediately', 'en'),
('important', 'en'),
('in', 'en'),
('inc', 'en'),
('inc.', 'en'),
('indeed', 'en'),
('index', 'en'),
('instead', 'en'),
('is', 'en'),
('isnt', 'en'),
('it', 'en'),
('its', 'en'),
('itself', 'en'),
('ive', 'en'),
('j', 'en'),
('just', 'en'),
('k', 'en'),
('keep', 'en'),
('kept', 'en'),
('kg', 'en'),
('km', 'en'),
('l', 'en'),
('largely', 'en'),
('later', 'en'),
('latter', 'en'),
('latterly', 'en'),
('least', 'en'),
('let', 'en'),
('lets', 'en'),
('like', 'en'),
('likely', 'en'),
('line', 'en'),
('ll', 'en'),
('ltd', 'en'),
('m', 'en'),
('mainly', 'en'),
('make', 'en'),
('makes', 'en'),
('many', 'en'),
('may', 'en'),
('maybe', 'en'),
('me', 'en'),
('means', 'en'),
('meantime', 'en'),
('meanwhile', 'en'),
('mg', 'en'),
('might', 'en'),
('miss', 'en'),
('ml', 'en'),
('more', 'en'),
('moreover', 'en'),
('most', 'en'),
('mostly', 'en'),
('mr', 'en'),
('mrs', 'en'),
('much', 'en'),
('mug', 'en'),
('must', 'en'),
('my', 'en'),
('myself', 'en'),
('n', 'en'),
('na', 'en'),
('namely', 'en'),
('nay', 'en'),
('nearly', 'en'),
('necessarily', 'en'),
('neither', 'en'),
('nevertheless', 'en'),
('next', 'en'),
('nine', 'en'),
('ninety', 'en'),
('no', 'en'),
('none', 'en'),
('nonetheless', 'en'),
('noone', 'en'),
('nor', 'en'),
('normally', 'en'),
('nos', 'en'),
('noted', 'en'),
('nothing', 'en'),
('not_not', 'en'),
('now', 'en'),
('nowhere', 'en'),
('o', 'en'),
('obtained', 'en'),
('of', 'en'),
('off', 'en'),
('oh', 'en'),
('omitted', 'en'),
('on', 'en'),
('one', 'en'),
('ones', 'en'),
('only', 'en'),
('onto', 'en'),
('ord', 'en'),
('or_or', 'en'),
('other', 'en'),
('others', 'en'),
('otherwise', 'en'),
('ought', 'en'),
('our', 'en'),
('ours', 'en'),
('ourselves', 'en'),
('out', 'en'),
('owing', 'en'),
('p', 'en'),
('page', 'en'),
('pages', 'en'),
('part', 'en'),
('particularly', 'en'),
('per', 'en'),
('perhaps', 'en'),
('please', 'en'),
('possible', 'en'),
('possibly', 'en'),
('potentially', 'en'),
('pp', 'en'),
('predominantly', 'en'),
('previously', 'en'),
('probably', 'en'),
('promptly', 'en'),
('put', 'en'),
('q', 'en'),
('quickly', 'en'),
('quite', 'en'),
('r', 'en'),
('ran', 'en'),
('rather', 'en'),
('re', 'en'),
('readily', 'en'),
('really', 'en'),
('recently', 'en'),
('ref', 'en'),
('refs', 'en'),
('related', 'en'),
('relatively', 'en'),
('respectively', 'en'),
('resulted', 'en'),
('s', 'en'),
('said', 'en'),
('same', 'en'),
('say', 'en'),
('sec', 'en'),
('seem', 'en'),
('seemed', 'en'),
('seeming', 'en'),
('seems', 'en'),
('seen', 'en'),
('seven', 'en'),
('shall', 'en'),
('she', 'en'),
('shes', 'en'),
('should', 'en'),
('shouldnt', 'en'),
('show', 'en'),
('showed', 'en'),
('shown', 'en'),
('showns', 'en'),
('shows', 'en'),
('significant', 'en'),
('significantly', 'en'),
('similar', 'en'),
('similarly', 'en'),
('so', 'en'),
('some', 'en'),
('somehow', 'en'),
('somethan', 'en'),
('something', 'en'),
('sometime', 'en'),
('sometimes', 'en'),
('somewhat', 'en'),
('somewhere', 'en'),
('soon', 'en'),
('strongly', 'en'),
('substantially', 'en'),
('successfully', 'en'),
('such', 'en'),
('sufficiently', 'en'),
('suggest', 'en'),
('t', 'en'),
('taking', 'en'),
('than', 'en'),
('that', 'en'),
('thatll', 'en'),
('thats', 'en'),
('thatve', 'en'),
('the', 'en'),
('their', 'en'),
('theirs', 'en'),
('them', 'en'),
('themselves', 'en'),
('then', 'en'),
('thence', 'en'),
('there', 'en'),
('thereby', 'en'),
('thered', 'en'),
('therefore', 'en'),
('therein', 'en'),
('therell', 'en'),
('thereof', 'en'),
('therere', 'en'),
('theres', 'en'),
('thereto', 'en'),
('thereupon', 'en'),
('thereve', 'en'),
('these', 'en'),
('they', 'en'),
('theyd', 'en'),
('theyll', 'en'),
('theyre', 'en'),
('theyve', 'en'),
('this', 'en'),
('those', 'en'),
('thou', 'en'),
('thoughh', 'en'),
('thousand', 'en'),
('throug', 'en'),
('throughout', 'en'),
('thru', 'en'),
('thus', 'en'),
('til', 'en'),
('tip', 'en'),
('to', 'en'),
('too', 'en'),
('try', 'en'),
('two', 'en'),
('u', 'en'),
('unless', 'en'),
('unlike', 'en'),
('unlikely', 'en'),
('until', 'en'),
('unto', 'en'),
('up', 'en'),
('upon', 'en'),
('ups', 'en'),
('us', 'en'),
('use', 'en'),
('usefully', 'en'),
('using', 'en'),
('usually', 'en'),
('v', 'en'),
('ve', 'en'),
('very', 'en'),
('via', 'en'),
('vol', 'en'),
('vols', 'en'),
('vs', 'en'),
('w', 'en'),
('was', 'en'),
('wasnt', 'en'),
('way', 'en'),
('we', 'en'),
('were', 'en'),
('werent', 'en'),
('weve', 'en'),
('what', 'en'),
('whatever', 'en'),
('whatll', 'en'),
('whats', 'en'),
('whatve', 'en'),
('when', 'en'),
('whenever', 'en'),
('where', 'en'),
('whereafter', 'en'),
('whereas', 'en'),
('whereby', 'en'),
('wherein', 'en'),
('wheres', 'en'),
('whereupon', 'en'),
('wherever', 'en'),
('whether', 'en'),
('which', 'en'),
('while', 'en'),
('whim', 'en'),
('whither', 'en'),
('who', 'en'),
('whod', 'en'),
('whoever', 'en'),
('wholl', 'en'),
('whom', 'en'),
('whomever', 'en'),
('whos', 'en'),
('whose', 'en'),
('why', 'en'),
('will', 'en'),
('with', 'en'),
('within', 'en'),
('without', 'en'),
('wont', 'en'),
('words', 'en'),
('would', 'en'),
('wouldnt', 'en'),
('www', 'en'),
('x', 'en'),
('y', 'en'),
('yes', 'en'),
('yet', 'en'),
('you', 'en'),
('youd', 'en'),
('youll', 'en'),
('your', 'en'),
('youre', 'en'),
('yours', 'en'),
('yourself', 'en'),
('yourselves', 'en'),
('youve', 'en'),
('z', 'en');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sdStopword`
--
ALTER TABLE `sdStopword`
  ADD PRIMARY KEY (`word`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
