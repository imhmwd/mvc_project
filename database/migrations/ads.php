<?php

return [
    "
--
-- Table structure for table `ads`
--

CREATE TABLE `ads` (
  `id` int(11) NOT NULL,
  `title` varchar(191) NOT NULL,
  `description` text NOT NULL,
  `address` text NOT NULL,
  `amount` varchar(191) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `view` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ads`
--
ALTER TABLE `ads`
ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ads`
--
ALTER TABLE `ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
    "
];