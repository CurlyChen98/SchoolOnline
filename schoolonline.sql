-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2018-08-26 19:20:22
-- 服务器版本： 10.1.34-MariaDB
-- PHP Version: 7.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `schoolonline`
--

-- --------------------------------------------------------

--
-- 表的结构 `class`
--

CREATE TABLE `class` (
  `cid` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `teacherkey` varchar(20) NOT NULL,
  `classkey` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `class`
--

INSERT INTO `class` (`cid`, `name`, `teacherkey`, `classkey`) VALUES
(1, '管理者组', 'AdminGoHiT', 'AdminGoHiC'),
(6, '17软件1', 'KGxuWJFYgIcB', 'KGLREW6A2I9F'),
(7, '17软件2', 'KGCsSjVzWyil', 'KGTSGVIQWD53');

-- --------------------------------------------------------

--
-- 表的结构 `class_student_list`
--

CREATE TABLE `class_student_list` (
  `id` int(11) NOT NULL,
  `student_id` varchar(25) NOT NULL,
  `student_name` varchar(20) NOT NULL,
  `class_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `class_student_list`
--

INSERT INTO `class_student_list` (`id`, `student_id`, `student_name`, `class_name`) VALUES
(1, '0104170101', '翁佳铭', '17软件1'),
(2, '0104170102', '赖鹏涛', '17软件1'),
(3, '0104170103', '詹浩涌', '17软件1'),
(4, '0104170104', '李朴一', '17软件1'),
(5, '0104170105', '蔡瑞俊', '17软件1'),
(6, '0104170106', '陈志东', '17软件1'),
(7, '0104170107', '杨俊朗', '17软件1'),
(8, '0104170108', '陈祈祈', '17软件1'),
(9, '0104170109', '莫茜茜', '17软件1'),
(10, '0104170110', '郑佳琳', '17软件1'),
(11, '0104170111', '徐永家', '17软件1'),
(12, '0104170112', '黄梓健', '17软件1'),
(13, '0104170113', '陈镇创', '17软件1'),
(14, '0104170114', '陈巧玲', '17软件1'),
(15, '0104170115', '梁玉徽', '17软件1'),
(16, '0104170116', '全振初', '17软件1'),
(17, '0104170117', '陈思敏', '17软件1'),
(18, '0104170118', '郭琳锺', '17软件1'),
(19, '0104170119', '黄镇澄', '17软件1'),
(20, '0104170120', '谢美芳', '17软件1'),
(21, '0104170121', '吴家健', '17软件1'),
(22, '0104170122', '梁海清', '17软件1'),
(23, '0104170123', '韦国康', '17软件1'),
(24, '0104170124', '江欣怡', '17软件1'),
(25, '0104170125', '胡嘉朗', '17软件1'),
(26, '0104170126', '钟思明', '17软件1'),
(27, '0104170127', '钟兆基', '17软件1'),
(28, '0104170128', '梁培煜', '17软件1'),
(29, '0104170129', '王锐', '17软件1'),
(30, '0104170130', '郑宝君', '17软件1'),
(31, '0104170131', '杨伟杰', '17软件1'),
(32, '0104170132', '刘振杰', '17软件1'),
(33, '0104170133', '姚泽棉', '17软件1'),
(34, '0104170134', '张海伦', '17软件1'),
(35, '0104170135', '肖进娣', '17软件1'),
(36, '0104170136', '丁晓锐', '17软件1'),
(37, '0104170137', '黎海钰', '17软件1'),
(38, '0104170138', '何伟军', '17软件1'),
(39, '0104170139', '陆文广', '17软件1'),
(40, '0104170140', '刘灿燊', '17软件1'),
(41, '0104170141', '何婷婷', '17软件1'),
(42, '0104170142', '陈俊颖', '17软件1'),
(43, '0104170143', '邓桂泳', '17软件1'),
(44, '0104170145', '程博伦', '17软件1'),
(45, '0104170146', '崔雨婷', '17软件1'),
(46, '0104170147', '谢振莹', '17软件1'),
(47, '0104170148', '吴贞云', '17软件1'),
(48, '0104170149', '舒松', '17软件1'),
(49, '0104170150', '罗文', '17软件1'),
(50, '0104170151', '汤宏慧', '17软件1'),
(51, '0104170152', '侯圣杰', '17软件1'),
(52, '0104170153', '李宜耕', '17软件1'),
(53, '0104170154', '张朝兴', '17软件1'),
(54, '0104170155', '李嘉炜', '17软件1'),
(55, '0104170201', '陈林', '17软件2'),
(56, '0104170202', '王伟丁', '17软件2'),
(57, '0104170203', '欧苗苗', '17软件2'),
(58, '0104170204', '郑立万', '17软件2'),
(59, '0104170205', '黎英伟', '17软件2'),
(60, '0104170206', '冯晓欣', '17软件2'),
(61, '0104170207', '黄模荣', '17软件2'),
(62, '0104170208', '张良策', '17软件2'),
(63, '0104170209', '饶文强', '17软件2'),
(64, '0104170210', '李佳琳', '17软件2'),
(65, '0104170211', '何文杰', '17软件2'),
(66, '0104170212', '郭泽浩', '17软件2'),
(67, '0104170213', '陈鲁', '17软件2'),
(68, '0104170214', '程鑫', '17软件2'),
(69, '0104170215', '江梓豪', '17软件2'),
(70, '0104170216', '黄明政', '17软件2'),
(71, '0104170217', '许卓生', '17软件2'),
(72, '0104170218', '梁泽恒', '17软件2'),
(73, '0104170219', '尤英山', '17软件2'),
(74, '0104170220', '周星达', '17软件2'),
(75, '0104170221', '宾灿坤', '17软件2'),
(76, '0104170222', '郭佳源', '17软件2'),
(77, '0104170223', '吴佳鑫', '17软件2'),
(78, '0104170224', '王嘉宝', '17软件2'),
(79, '0104170225', '何宗霖', '17软件2'),
(80, '0104170226', '薛清槟', '17软件2'),
(81, '0104170227', '钟浩鹏', '17软件2'),
(82, '0104170228', '曹杰榆', '17软件2'),
(83, '0104170229', '许世杰', '17软件2'),
(84, '0104170230', '陈茂洁', '17软件2'),
(85, '0104170231', '卢家铭', '17软件2'),
(86, '0104170232', '李美霖', '17软件2'),
(87, '0104170233', '邱梓嘉', '17软件2'),
(88, '0104170234', '黄春平', '17软件2'),
(89, '0104170235', '谢杰锋', '17软件2'),
(90, '0104170236', '潘健雄', '17软件2'),
(91, '0104170237', '曹家辉', '17软件2'),
(92, '0104170238', '彭海', '17软件2'),
(93, '0104170239', '蔡楚乐', '17软件2'),
(94, '0104170240', '廖剑锋', '17软件2'),
(95, '0104170241', '冯志斌', '17软件2'),
(96, '0104170242', '梁铭泉', '17软件2'),
(97, '0104170243', '尹银娣', '17软件2'),
(98, '0104170244', '罗斯乾', '17软件2'),
(99, '0104170245', '郑锐朗', '17软件2'),
(100, '0104170246', '林倬', '17软件2'),
(101, '0104170247', '张焕然', '17软件2'),
(102, '0104170248', '陈奕涠', '17软件2'),
(103, '0104170249', '高文江', '17软件2'),
(104, '0104170250', '韦晓科', '17软件2'),
(105, '0104170251', '姚篮', '17软件2'),
(106, '0104170252', '郭蒙蒙', '17软件2');

-- --------------------------------------------------------

--
-- 表的结构 `course`
--

CREATE TABLE `course` (
  `couid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `title` varchar(20) NOT NULL,
  `date` datetime NOT NULL,
  `content` longtext NOT NULL,
  `video_src` longtext NOT NULL,
  `video_kind` int(11) NOT NULL,
  `task_src` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `s_use`
--

CREATE TABLE `s_use` (
  `uid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `opid` varchar(50) NOT NULL,
  `name` varchar(20) NOT NULL,
  `level` varchar(10) NOT NULL DEFAULT '1',
  `lastdate` datetime NOT NULL,
  `password` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `talk`
--

CREATE TABLE `talk` (
  `taid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `couid` int(11) NOT NULL,
  `title` varchar(30) NOT NULL,
  `detail` text NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `talkdet`
--

CREATE TABLE `talkdet` (
  `taid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `detail` text NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `task`
--

CREATE TABLE `task` (
  `task_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `couid` int(11) NOT NULL,
  `task_url` longtext NOT NULL,
  `task_name` varchar(50) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`cid`) USING BTREE;

--
-- Indexes for table `class_student_list`
--
ALTER TABLE `class_student_list`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`couid`) USING BTREE;

--
-- Indexes for table `s_use`
--
ALTER TABLE `s_use`
  ADD PRIMARY KEY (`uid`) USING BTREE;

--
-- Indexes for table `talk`
--
ALTER TABLE `talk`
  ADD PRIMARY KEY (`taid`) USING BTREE;

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`task_id`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `class`
--
ALTER TABLE `class`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用表AUTO_INCREMENT `class_student_list`
--
ALTER TABLE `class_student_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- 使用表AUTO_INCREMENT `course`
--
ALTER TABLE `course`
  MODIFY `couid` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `s_use`
--
ALTER TABLE `s_use`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `talk`
--
ALTER TABLE `talk`
  MODIFY `taid` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `task`
--
ALTER TABLE `task`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
