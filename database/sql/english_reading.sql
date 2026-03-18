-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: english_reading
-- ------------------------------------------------------
-- Server version	9.5.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '85dc8b77-b958-11f0-938e-e86a642b8f60:1-332';

--
-- Table structure for table `article_tags`
--

DROP TABLE IF EXISTS `article_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `article_tags` (
  `article_id` int NOT NULL,
  `tag_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`article_id`,`tag_id`),
  UNIQUE KEY `unique_article_tag` (`article_id`,`tag_id`),
  KEY `tag_id` (`tag_id`),
  CONSTRAINT `article_tags_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`article_id`) ON DELETE CASCADE,
  CONSTRAINT `article_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO article_tags (article_id, tag_id, created_at) VALUES
-- Article 1: Carbon-Negative Concrete (CE, Easy)
(1, 7, '2026-03-18 10:31:00'),
(1, 13, '2026-03-18 10:31:00'),
(1, 11, '2026-03-18 10:31:00'),
(1, 15, '2026-03-18 10:31:00'),
-- Article 2: Bacteria Self-Healing Concrete (CE, Easy)
(2, 7, '2026-03-18 10:31:00'),
(2, 13, '2026-03-18 10:31:00'),
(2, 15, '2026-03-18 10:31:00'),
-- Article 3: Cement vs Concrete (CE, Intermediate)
(3, 5, '2026-03-18 10:31:00'),
(3, 7, '2026-03-18 10:31:00'),
(3, 11, '2026-03-18 10:31:00'),
(3, 15, '2026-03-18 10:31:00'),
-- Article 4: Concrete Emissions (CE, Intermediate)
(4, 5, '2026-03-18 10:31:00'),
(4, 7, '2026-03-18 10:31:00'),
(4, 18, '2026-03-18 10:31:00'),
(4, 15, '2026-03-18 10:31:00'),
-- Article 5: Earthquake-Resistant Building (CE, Advanced)
(5, 5, '2026-03-18 10:31:00'),
(5, 7, '2026-03-18 10:31:00'),
(5, 19, '2026-03-18 10:31:00'),
(5, 6, '2026-03-18 10:31:00'),
-- Article 6: California Bridge (CE, Advanced)
(6, 5, '2026-03-18 10:31:00'),
(6, 7, '2026-03-18 10:31:00'),
(6, 19, '2026-03-18 10:31:00'),
-- Article 7: Algebra Oldest Problem (Math, Easy)
(7, 8, '2026-03-18 10:31:00'),
-- Article 8: James Maynard (Math, Easy)
(8, 8, '2026-03-18 10:31:00'),
-- Article 9: Bell Curves (Math, Intermediate)
(9, 5, '2026-03-18 10:31:00'),
(9, 8, '2026-03-18 10:31:00'),
(9, 17, '2026-03-18 10:31:00'),
-- Article 10: Game Theory Algorithms (Math, Intermediate)
(10, 5, '2026-03-18 10:31:00'),
(10, 8, '2026-03-18 10:31:00'),
(10, 9, '2026-03-18 10:31:00'),
-- Article 11: Tracy-Widom Distribution (Math, Advanced)
(11, 5, '2026-03-18 10:31:00'),
(11, 8, '2026-03-18 10:31:00'),
-- Article 12: Serfaty Interview (Math, Advanced)
(12, 5, '2026-03-18 10:31:00'),
(12, 8, '2026-03-18 10:31:00'),
-- Article 13: Generative AI Explained (CS, Easy)
(13, 9, '2026-03-18 10:31:00'),
(13, 12, '2026-03-18 10:31:00'),
(13, 6, '2026-03-18 10:31:00'),
-- Article 14: Robot Helping Humans (CS, Easy)
(14, 9, '2026-03-18 10:31:00'),
(14, 12, '2026-03-18 10:31:00'),
(14, 16, '2026-03-18 10:31:00'),
-- Article 15: AI Environmental Impact (CS, Intermediate)
(15, 5, '2026-03-18 10:31:00'),
(15, 9, '2026-03-18 10:31:00'),
(15, 18, '2026-03-18 10:31:00'),
(15, 14, '2026-03-18 10:31:00'),
-- Article 16: Robot Perception (CS, Intermediate)
(16, 5, '2026-03-18 10:31:00'),
(16, 9, '2026-03-18 10:31:00'),
(16, 16, '2026-03-18 10:31:00'),
(16, 12, '2026-03-18 10:31:00'),
-- Article 17: Quantum vs Classical (CS, Advanced)
(17, 5, '2026-03-18 10:31:00'),
(17, 9, '2026-03-18 10:31:00'),
(17, 6, '2026-03-18 10:31:00'),
(17, 8, '2026-03-18 10:31:00'),
-- Article 18: Graph Traversal (CS, Advanced)
(18, 5, '2026-03-18 10:31:00'),
(18, 9, '2026-03-18 10:31:00'),
(18, 8, '2026-03-18 10:31:00'),
-- Article 19: 3D Printing Materials (ME, Easy)
(19, 7, '2026-03-18 10:31:00'),
(19, 13, '2026-03-18 10:31:00'),
(19, 6, '2026-03-18 10:31:00'),
-- Article 20: Nanotechnology (ME, Easy)
(20, 7, '2026-03-18 10:31:00'),
(20, 13, '2026-03-18 10:31:00'),
(20, 6, '2026-03-18 10:31:00'),
-- Article 21: Thermal Energy Storage (ME, Intermediate)
(21, 5, '2026-03-18 10:31:00'),
(21, 7, '2026-03-18 10:31:00'),
(21, 14, '2026-03-18 10:31:00'),
-- Article 22: Humanoid Robots (ME, Intermediate)
(22, 5, '2026-03-18 10:31:00'),
(22, 7, '2026-03-18 10:31:00'),
(22, 16, '2026-03-18 10:31:00'),
(22, 6, '2026-03-18 10:31:00'),
-- Article 23: Robots at Work (ME, Advanced)
(23, 5, '2026-03-18 10:31:00'),
(23, 7, '2026-03-18 10:31:00'),
(23, 16, '2026-03-18 10:31:00'),
(23, 12, '2026-03-18 10:31:00'),
-- Article 24: Stellarator Fusion (ME, Advanced)
(24, 5, '2026-03-18 10:31:00'),
(24, 7, '2026-03-18 10:31:00'),
(24, 14, '2026-03-18 10:31:00'),
(24, 6, '2026-03-18 10:31:00'),
-- Article 25: NASA Electric Flight (ME+T, Easy)
(25, 7, '2026-03-18 10:31:00'),
(25, 10, '2026-03-18 10:31:00'),
(25, 14, '2026-03-18 10:31:00'),
-- Article 26: Eviation Electric Aviation (ME+T, Easy)
(26, 7, '2026-03-18 10:31:00'),
(26, 10, '2026-03-18 10:31:00'),
(26, 6, '2026-03-18 10:31:00'),
-- Article 27: Autonomous Eco-Driving (ME+T, Intermediate)
(27, 5, '2026-03-18 10:31:00'),
(27, 10, '2026-03-18 10:31:00'),
(27, 12, '2026-03-18 10:31:00'),
(27, 20, '2026-03-18 10:31:00'),
-- Article 28: Axial-Flux Motor (ME+T, Intermediate)
(28, 5, '2026-03-18 10:31:00'),
(28, 7, '2026-03-18 10:31:00'),
(28, 10, '2026-03-18 10:31:00'),
(28, 20, '2026-03-18 10:31:00'),
-- Article 29: EV Batteries (ME+T, Advanced)
(29, 5, '2026-03-18 10:31:00'),
(29, 7, '2026-03-18 10:31:00'),
(29, 13, '2026-03-18 10:31:00'),
(29, 14, '2026-03-18 10:31:00'),
(29, 20, '2026-03-18 10:31:00'),
-- Article 30: Airbus Superconducting Aircraft (ME+T, Advanced)
(30, 5, '2026-03-18 10:31:00'),
(30, 7, '2026-03-18 10:31:00'),
(30, 10, '2026-03-18 10:31:00'),
(30, 14, '2026-03-18 10:31:00'),
(30, 6, '2026-03-18 10:31:00');
--
-- Dumping data for table `article_tags`
--

LOCK TABLES `article_tags` WRITE;
/*!40000 ALTER TABLE `article_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `article_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `articles`
--

DROP TABLE IF EXISTS `articles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `articles` (
  `article_id` int NOT NULL AUTO_INCREMENT,
  `subject` enum('Civil Engineering','Mathematics','Computer Science','Mechanical Engineering','Mechanical Engineering with Transportation') COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` enum('Easy','Intermediate','Advanced') COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_count` int DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`article_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- ----------------------------------------
-- Subject 1: Civil Engineering (articles 1-6)
-- ----------------------------------------
INSERT INTO articles (article_id, subject, title, slug, content, author, source, level, read_count, deleted_at, created_at, updated_at) VALUES
(1, 'Civil Engineering',
 'New Carbon-Negative Material Could Make Concrete and Cement More Sustainable',
 'new-carbon-negative-material-concrete-sustainable',
 'Using seawater, electricity and carbon dioxide (CO2), Northwestern University scientists have developed a new carbon-negative building material.

As Earth's climate continues to warm, researchers around the globe are exploring ways to capture CO2 from the air and store it deep underground. While this approach has multiple climate benefits, it does not maximize the value of the enormous amounts of atmospheric CO2.

Now, Northwestern's new strategy addresses this challenge by locking away CO2 permanently and turning it into valuable materials, which can be used to manufacture concrete, cement, plaster and paint. The process to generate the carbon-negative materials also releases hydrogen gas -- a clean fuel with various applications, including transportation.

The study will be published on March 19 in the journal Advanced Sustainable Systems.

"We have developed a new approach that allows us to use seawater to create carbon-negative construction materials," said Northwestern's Alessandro Rotta Loria, who led the study. "Cement, concrete, paint and plasters are customarily composed of or derived from calcium- and magnesium-based minerals, which are often sourced from aggregates -- what we call sand. Currently, sand is sourced through mining from mountains, riverbeds, coasts and the ocean floor. In collaboration with Cemex, we have devised an alternative approach to source sand -- not by digging into the Earth but by harnessing electricity and CO2 to grow sand-like materials in seawater."

Rotta Loria is the Louis Berger Assistant Professor of Civil and Environmental Engineering at Northwestern's McCormick School of Engineering. Jeffrey Lopez, an assistant professor of chemical and biological engineering at McCormick, served as a key coauthor on the study. Co-advised by Rotta Loria and Lopez, other Northwestern contributors include Nishu Devi, a postdoctoral fellow and lead author; Xiaohui Gong and Daiki Shoji, Ph.D. students; and Amy Wagner, former graduate student. The study also benefited from the contributions of key representatives from the Global R&D department of Cemex, a global building materials company dedicated to sustainable construction. This work is part of a broader collaboration between Northwestern and Cemex.

Seashell-inspired science

The new study builds on previous work from Rotta Loria's lab to store CO2 long term in concrete and to electrify seawater to cement marine soils. Now, he leverages insights from those two projects by injecting CO2while applying electricity to seawater in the lab.

"Our research group tries to harness electricity to innovate construction and industrial processes," Rotta Loria said. "We also like to use seawater because it's a naturally abundant resource. It's not scarce like fresh water."

To generate the carbon-negative material, the researchers started by inserting electrodes into seawater and applying an electric current. The low electrical current split water molecules into hydrogen gas and hydroxide ions. While leaving the electric current on, the researchers bubbled CO2 gas through seawater. This process changed the chemical composition of the water, increasing the concentration of bicarbonate ions.

Finally, the hydroxide ions and bicarbonate ions reacted with other dissolved ions, such as calcium and magnesium, that occur naturally in seawater. The reaction produced solid minerals, including calcium carbonate and magnesium hydroxide. Calcium carbonate directly acts as a carbon sink, while magnesium hydroxide sequesters carbon through further interactions with CO2.

Rotta Loria likens the process to the technique coral and mollusks use to form their shells, which harnesses metabolic energy to convert dissolved ions into calcium carbonate. But, instead of metabolic energy, the researchers applied electrical energy to initiate the process and boosted mineralization with the injection of CO2.

Dual discoveries

Through experimentation, the researchers made two significant discoveries. Not only could they grow these minerals into sand, but they also were able to change the composition of these materials by controlling experimental factors, including the voltage and current of electricity, the flow rate, timing and duration of CO2injection, and the flow rate, timing and duration of seawater recirculation in the reactor.

Depending on the conditions, the resulting substances are flakier and more porous or denser and harder -- but always primarily composed of calcium carbonate and/or magnesium hydroxide. Researchers can grow the materials around an electrode or directly in solution.

"We showed that when we generate these materials, we can fully control their properties, such as the chemical composition, size, shape and porosity," Rotta Loria said. "That gives us some flexibility to develop materials suited to different applications."

These materials could be used in concrete as a substitute for sand and/or gravel -- a crucial ingredient that accounts for 60-70% of this ubiquitous building material. Or they could be used to manufacture cement, plaster and paint -- all essential finishes in the built environment.

Storing carbon in structures

Depending on the ratio of minerals, the material can hold over half its weight in CO2. With a composition of half calcium carbonate and half magnesium hydroxide, for example, 1 metric ton of the material has the capacity to store over one-half a metric ton of CO2. Rotta Loria also says the material -- if used to replace sand or powder -- would not weaken the strength of concrete or cement.

Rotta Loria envisions industry could apply the technique in highly scalable, modular reactors -- not directly into the ocean -- to avoid disturbing ecosystems and sea life.

"This approach would enable full control of the chemistry of the water sources and water effluent, which would be reinjected into open seawater only after adequate treatment and environmental verifications," he said.

Responsible for 8% of global CO2 emissions, the cement industry is the world's fourth-largest carbon emitter, according to the World Economic Forum. When combined with concrete production, this figure is even higher. Rotta Loria foresees putting some of that CO2 back into concrete and cement to make more sustainable materials for construction and manufacturing.

"We could create a circularity where we sequester CO2 right at the source," Rotta Loria said. "And, if the concrete and cement plants are located on shorelines, we could use the ocean right next to them to feed dedicated reactors where CO2 is transformed through clean electricity into materials that can be used for myriad applications in the construction industry. Then, those materials would truly become carbon sinks."
',
 'Amanda Morris',
 'ScienceDaily',
 'Easy', 0, NULL, '2026-03-18 10:01:00', '2026-03-18 10:01:00'),
 
(2, 'Civil Engineering',
 'Veins of Bacteria Could Form a Self-Healing System for Concrete Infrastructure',
 'veins-of-bacteria-self-healing-concrete-infrastructure',
 'In hopes of producing concrete structures that can repair their cracks, researchers from Drexel University's College of Engineering are putting a new twist on an old trick for improving the durability of concrete. Fiber reinforcement has been around since the first masons were mixing horsehair into their mud. But the Drexel research team is taking this method to the next level by turning reinforcing fibers into a living tissue system that rushes concrete-healing bacteria to the site of cracks to repair the damage.

Recently reported in the journal Construction and Building Materials, Drexel's "BioFiber" is a polymer fiber encased in a bacteria-laden hydrogel and a protective, damage-responsive shell. The team reports that a grid of BioFibers embedded within a concrete structure can improve its durability, prevent cracks from growing and enable self-healing.

"This is an exciting development for the ongoing efforts to improve building materials using inspiration from nature," said Amir Farnam, PhD, an associate professor in the College of Engineering who was a leader of the research team. "We are seeing every day that our ageing concrete structures are experiencing damage which lowers their functional life and requires critical repairs that are costly. Imagine, they can heal themselves? In our skin, our tissue does it naturally through multilayer fibrous structure infused with our self-healing fluid -- blood. These biofibers mimic this concept and use stone-making bacteria to create damage-responsive living self-healing concrete."

Lengthening the lifespan of concrete is not just a benefit for the building sector, it's become a priority for countries around the world that are working to reduce greenhouse gas. The process of making the ingredients of concrete -- burning a mixture of minerals, such as limestone, clay or shale at temperatures in excess of 2,000 degrees Fahrenheit -- accounts for 8% of global greenhouse gas emissions.

Concrete structures can degrade in as little as 50 years depending on their environment. Between replacements and the growing demand for new buildings, concrete is the most consumed and most in-demand building material in the world.

Producing concrete that can last longer would be a big step in reducing its contribution to global warming, not to mention reducing the long-term cost of infrastructure repairs, which is why the U.S. Department of Energy has recently launched efforts focused on improving it.

Over the last decade, Drexel has led the way in looking at how to improve concrete's sustainability and durability, and Farnam's lab is part of a team participating in a Department of Defense effort to fortify its aging structures.

"For several years, the concept of bio-self-healing cementitious composites has been nurtured within the Advanced Infrastructure Materials Lab," said Mohammad Houshmand, a doctoral candidate in Farnam's lab who was the lead author of the research. "The BioFiber project represents a collaborative, multidisciplinary endeavor, integrating expertise from the fields of civil engineering, biology, chemistry, and materials science. The primary objective is to pioneer the development of a multifunctional self-healing BioFiber technology, setting new standards at the intersection of these diverse disciplines."

The team's approach in creating BioFibers was inspired by skin tissue's self-healing capability and vasculature system's role in helping organisms heal their own wounds. And it uses a biological technique they developed to enable self-repairing in concrete infrastructure with the help of biomineralizing bacteria.

In collaboration with research teams led by Caroline Schauer, PhD, the Margaret C. Burns Chair in Engineering, Christopher Sales, PhD, an associate professor, and Ahmad Najafi, PhD, an assistant professor, all from the College of Engineering, the group identified a strain of Lysinibacillus sphaericus bacteria as a bio-healing agent for the fiber. The durable bacteria, typically found in the soil, has the ability to drive a biological process called microbial induced calcium carbonate precipitation to create a stone-like material that can stabilize and harden into a patch for exposed cracks in concrete.

When induced into forming an endospore the bacteria can survive the harsh conditions inside concrete, lying dormant until called into action.

"One of the amazing things about this research is how everyone comes at the problem from their different expertise and the solutions to creating novel BioFibers are so much stronger because of that," Schauer said. "Selecting the right combination of bacteria, hydrogel and polymer coating was central to this research and to the functionality of BioFiber. Drawing inspiration from nature is one thing, but translating that into an application comprised of biological ingredients that can all coexist in a functional structure is quite an undertaking -- one that required a multifaced team of experts to successfully achieve."

To assemble the BioFiber, the team started with a polymer fiber core capable of stabilizing and supporting concrete structures. It coated the fiber with a layer of endospore-laden hydrogel and encased the entire assembly with a damage-responsive polymer shell, like skin tissues. The entire assembly is a little over half a millimeter thick.

Placed in a grid throughout the concrete as it is poured, the BioFiber acts as a reinforcing support agent. But its true talents are revealed only when a crack penetrates the concrete enough to pierce the fiber's outer polymer shell.
As water makes its way into the crack, eventually reaching the BioFiber, it causes the hydrogel to expand and push its way out of the shell and up toward the surface of the crack. While this is happening, the bacteria are activated from their endospore form in the presence of carbon and a nutrient source in the concrete. Reacting with the calcium in the concrete, the bacteria produce calcium carbonate which acts as a cementing material to fill the crack all the way to the surface.
The healing time ultimately depends on the size of the crack and activity of the bacteria -- a mechanism the team is currently studying -- but early indications suggest the bacteria could do its job in as little as one to two days.
"While there is much work to be done in examining the kinetics of self-repair, our findings suggest that this is a viable method for arresting formation, stabilizing and repairing cracks without external intervention," Farnam said. "This means that BioFiber could one day be used to make a 'living' concrete infrastructure and extend its life, preventing the need for costly repairs or replacements."
',
 'Drexel University',
 'ScienceDaily',
 'Easy', 0, NULL, '2026-03-18 10:02:00', '2026-03-18 10:02:00'),
 
(3, 'Civil Engineering',
 'Explained: Cement vs. Concrete — Their Differences, and Opportunities for Sustainability',
 'explained-cement-vs-concrete-differences-sustainability',
 'There’s a lot the average person doesn’t know about concrete. For example, it’s porous; it’s the world’s most-used material after water; and, perhaps most fundamentally, it’s not cement.

Though many use "cement" and "concrete" interchangeably, they actually refer to two different — but related — materials: Concrete is a composite made from several materials, one of which is cement.

Cement production begins with limestone, a sedimentary rock. Once quarried, it is mixed with a silica source, such as industrial byproducts slag or fly ash, and gets fired in a kiln at 2,700 degrees Fahrenheit. What comes out of the kiln is called clinker. Cement plants grind clinker down to an extremely fine powder and mix in a few additives. The final result is cement.

“Cement is then brought to sites where it is mixed with water, where it becomes cement paste,” explains Professor Franz-Josef Ulm, faculty director of the MIT Concrete Sustainability Hub (CSHub). “If you add sand to that paste it becomes mortar. And if you add to the mortar large aggregates — stones of a diameter of up to an inch — it becomes concrete.”

What makes concrete so strong is the chemical reaction that occurs when cement and water mix — a process known as hydration.

“Hydration occurs when cement and water react,” says Ulm. “During hydration, the clinker dissolves into the calcium and recombines with water and silica to form calcium silica hydrates.”

Calcium silica hydrates, or CSH, are the key to cement’s solidity. As they form, they combine, developing tight bonds that lend strength to the material. These connections have a surprising byproduct — they make cement incredibly porous.

Within the spaces between the bonds of CSH, tiny pores develop — on the scale of 3 nanometers. These are known as gel pores. On top of this, any water that hasn’t reacted to form CSH during the hydration process remains in the cement, creating another set of larger pores, called capillary pores.

According to research conducted by CSHub, the French National Center for Scientific Research, and Aix-Marseille University, cement paste is so porous that 96 percent of its pores are connected.

Despite this porosity, cement possesses excellent strength and binding properties. Of course, by decreasing this porosity, one can create a denser and even stronger final product.

Starting in the 1980s, engineers designed a material — high-performance concrete (HPC) — that did just that.

“High-performance concrete developed in the 1980s when people realized that the capillary pores can be reduced in part by reducing the water-to-cement ratio,” says Ulm. “With the addition of certain ingredients as well, this created more CSH and reduced the water that remained after hydration. Essentially, it reduced the larger pores filled with water and increased the strength of the material.”

Of course, notes Ulm, reducing the water-to-cement ratio for HPC also requires more cement. And depending on how that cement is produced, this can increase the material’s environmental impact. This is in part because when calcium carbonate is fired in a kiln to produce conventional cement, a chemical reaction occurs that produces carbon dioxide (CO2).

Another source of cement’s CO2 emissions come from heating cement kilns. This heating must be done using fossil fuels because of the extremely high temperatures required in the kiln (2,700 F). The electrification of kilns is being studied, but it is currently not technically or economically feasible.

Since concrete is the most popular material in the world and cement is the primary binder used in concrete, these two sources of CO2 are the main reason that cement contributes around 8 percent of global emissions.

CSHub’s Executive Director Jeremy Gregory, however, sees concrete’s scale as an opportunity to mitigate climate change.

“Concrete is the most-used building material in the world. And because we use so much of it, any reductions we make in its footprint will have a big impact on global emissions.”

Many of the technologies needed to reduce concrete’s footprint exist today, he notes.

“When it comes to reducing the emissions of cement, we can increase the efficiency of cement kilns by increasing our use of waste materials as energy sources rather than fossil fuels,” explains Gregory.

“We can also use blended cements that have less clinker, such as Portland limestone cement, which mixes unheated limestone in the final grinding step of cement production. The last thing we can do is capture and store or utilize the carbon emitted during cement production.”

Carbon capture, utilization, and storage has significant potential to reduce cement and concrete’s environmental impact while creating large market opportunities. According to the Center for Climate and Energy Solutions, carbon utilization in concrete will have a $400 billion global market by 2030. Several companies, like Solidia Technologies and Carbon Cure, are getting ahead of the curve by designing cement and concrete that utilize and consequentially sequester CO2 during the production process.

“What’s clear, though,” says Gregory, “is that low-carbon concrete mixtures will have to use many of these strategies. This means we need to rethink how we design our concrete mixtures.”

Currently, the exact specifications of concrete mixtures are prescribed ahead of time. While this reduces the risk for developers, it also hinders innovative mixes that lower emissions.

As a solution, Gregory advocates specifying a mix’s performance rather than its ingredients.

“Many prescriptive requirements limit the ability to improve concrete’s environmental impact — such as limits on the water-to-cement ratio and the use of waste materials in the mixture,” he explains. “Shifting to performance-based specifications is a key technique for encouraging more innovation and meeting cost and environmental impact targets.”

According to Gregory, this requires a culture shift. To transition to performance-based specifications, numerous stakeholders, such as architects, engineers, and specifiers, will have to collaborate to design the optimal mix for their project rather than rely on a predesigned mix.

To encourage other drivers of low-carbon concrete, says Gregory, “we [also] need to address barriers of risk and cost. We can mitigate risk by asking producers to report the environmental footprints of their products and by enabling performance-based specifications. To address cost, we need to support the development and deployment of carbon capture and low-carbon technologies.”

While innovations can reduce concrete’s initial emissions, concrete can also reduce emissions in other ways.

One way is through its use. The application of concrete in buildings and infrastructure can enable lower greenhouse gas emissions over time. Concrete buildings, for instance, can have high energy efficiency, while the surface and structural properties of concrete pavements allow cars to consume less fuel.

Concrete can also reduce some of its initial impact through exposure to the air.   

“Something unique about concrete is that it actually absorbs carbon over its life during a natural chemical process called carbonation,” says Gregory.

Carbonation occurs gradually in concrete as CO2 in the air reacts with cement to form water and calcium carbonate. A 2016 paper in Nature Geoscience found that since 1930, carbonation in concrete has offset 43 percent of the emissions from the chemical transformation of calcium carbonate to clinker during cement production.

Carbonation, though, has a drawback. It can lead to the corrosion of the steel rebar often set within concrete. Going forward, engineers may seek to maximize the carbon uptake of the carbonation process while also minimizing the durability issues it can pose.

Carbonation, as well as technologies like carbon capture, utilization, and storage and improved mixes, will all contribute to lower-carbon concrete. But making this possible will require the cooperation of academia, industry, and the government, says Gregory.

He sees this as an opportunity.

“Change doesn’t have to happen based on just technology,” he notes. “It can also happen by how we work together toward common objectives.”
',
 'Andrew Logan',
 'MIT News',
 'Intermediate', 0, NULL, '2026-03-18 10:03:00', '2026-03-18 10:03:00'),
 
(4, 'Civil Engineering',
 'Concrete''s Role in Reducing Building and Pavement Emissions',
 'concretes-role-reducing-building-pavement-emissions',
 'As the most consumed material after water, concrete is indispensable to the many essential systems — from roads to buildings — in which it is used.

But due to its extensive use, concrete production also contributes to around 1 percent of emissions in the United States and remains one of several carbon-intensive industries globally. Tackling climate change, then, will mean reducing the environmental impacts of concrete, even as its use continues to increase.

In a new paper in the Proceedings of the National Academy of Sciences, a team of current and former researchers at the MIT Concrete Sustainability Hub (CSHub) outlines how this can be achieved.

They present an extensive life-cycle assessment of the building and pavements sectors that estimates how greenhouse gas (GHG) reduction strategies — including those for concrete and cement — could minimize the cumulative emissions of each sector and how those reductions would compare to national GHG reduction targets. 

The team found that, if reduction strategies were implemented, the emissions for pavements and buildings between 2016 and 2050 could fall by up to 65 percent and 57 percent, respectively, even if concrete use accelerated greatly over that period. These are close to U.S. reduction targets set as part of the Paris Climate Accords. The solutions considered would also enable concrete production for both sectors to attain carbon neutrality by 2050.

Despite continued grid decarbonization and increases in fuel efficiency, they found that the vast majority of the GHG emissions from new buildings and pavements during this period would derive from operational energy consumption rather than so-called embodied emissions — emissions from materials production and construction.

Sources and solutions

The consumption of concrete, due to its versatility, durability, constructability, and role in economic development, has been projected to increase around the world.

While it is essential to consider the embodied impacts of ongoing concrete production, it is equally essential to place these initial impacts in the context of the material’s life cycle.

Due to concrete’s unique attributes, it can influence the long-term sustainability performance of the systems in which it is used. Concrete pavements, for instance, can reduce vehicle fuel consumption, while concrete structures can endure hazards without needing energy- and materials-intensive repairs.

Concrete’s impacts, then, are as complex as the material itself — a carefully proportioned mixture of cement powder, water, sand, and aggregates. Untangling concrete’s contribution to the operational and embodied impacts of buildings and pavements is essential for planning GHG reductions in both sectors.

Set of scenarios

In their paper, CSHub researchers forecast the potential greenhouse gas emissions from the building and pavements sectors as numerous emissions reduction strategies were introduced between 2016 and 2050.

Since both of these sectors are immense and rapidly evolving, modeling them required an intricate framework.

“We don’t have details on every building and pavement in the United States,” explains Randolph Kirchain, a research scientist at the Materials Research Laboratory and co-director of CSHub.

“As such, we began by developing reference designs, which are intended to be representative of current and future buildings and pavements. These were adapted to be appropriate for 14 different climate zones in the United States and then distributed across the U.S. based on data from the U.S. Census and the Federal Highway Administration”

To reflect the complexity of these systems, their models had to have the highest resolutions possible.

“In the pavements sector, we collected the current stock of the U.S. network based on high-precision 10-mile segments, along with the surface conditions, traffic, thickness, lane width, and number of lanes for each segment,” says Hessam AzariJafari, a postdoc at CSHub and a co-author on the paper.

“To model future paving actions over the analysis period, we assumed four climate conditions; four road types; asphalt, concrete, and composite pavement structures; as well as major, minor, and reconstruction paving actions specified for each climate condition.”

Using this framework, they analyzed a “projected” and an “ambitious” scenario of reduction strategies and system attributes for buildings and pavements over the 34-year analysis period. The scenarios were defined by the timing and intensity of GHG reduction strategies.

As its name might suggest, the projected scenario reflected current trends. For the building sector, solutions encompassed expected grid decarbonization and improvements to building codes and energy efficiency that are currently being implemented across the country. For pavements, the sole projected solution was improvements to vehicle fuel economy. That’s because as vehicle efficiency continues to increase, excess vehicle emissions due to poor road quality will also decrease.

Both the projected scenarios for buildings and pavements featured the gradual introduction of low-carbon concrete strategies, such as recycled content, carbon capture in cement production, and the use of captured carbon to produce aggregates and cure concrete.

“In the ambitious scenario,” explains Kirchain, “we went beyond projected trends and explored reasonable changes that exceed current policies and [industry] commitments.”

Here, the building sector strategies were the same, but implemented more aggressively. The pavements sector also abided by more aggressive targets and incorporated several novel strategies, including investing more to yield smoother roads, selectively applying concrete overlays to produce stiffer pavements, and introducing more reflective pavements — which can change the Earth’s energy balance by sending more energy out of the atmosphere.

Results

As the grid becomes greener and new homes and buildings become more efficient, many experts have predicted the operational impacts of new construction projects to shrink in comparison to their embodied emissions.

“What our life-cycle assessment found,” says Jeremy Gregory, the executive director of the MIT Climate Consortium and the lead author on the paper, “is that [this prediction] isn’t necessarily the case.”

“Instead, we found that more than 80 percent of the total emissions from new buildings and pavements between 2016 and 2050 would derive from their operation.”

In fact, the study found that operations will create the majority of emissions through 2050 unless all energy sources — electrical and thermal — are carbon-neutral by 2040. This suggests that ambitious interventions to the electricity grid and other sources of operational emissions can have the greatest impact.

Their predictions for emissions reductions generated additional insights.  

For the building sector, they found that the projected scenario would lead to a reduction of 49 percent compared to 2016 levels, and that the ambitious scenario provided a 57 percent reduction.

As most buildings during the analysis period were existing rather than new, energy consumption dominated emissions in both scenarios. Consequently, decarbonizing the electricity grid and improving the efficiency of appliances and lighting led to the greatest improvements for buildings, they found.

In contrast to the building sector, the pavements scenarios had a sizeable gulf between outcomes: the projected scenario led to only a 14 percent reduction while the ambitious scenario had a 65 percent reduction — enough to meet U.S. Paris Accord targets for that sector. This gulf derives from the lack of GHG reduction strategies being pursued under current projections.

“The gap between the pavement scenarios shows that we need to be more proactive in managing the GHG impacts from pavements,” explains Kirchain. “There is tremendous potential, but seeing those gains requires action now.”

These gains from both ambitious scenarios could occur even as concrete use tripled over the analysis period in comparison to the projected scenarios — a reflection of not only concrete’s growing demand but its potential role in decarbonizing both sectors.

Though only one of their reduction scenarios (the ambitious pavement scenario) met the Paris Accord targets, that doesn’t preclude the achievement of those targets: many other opportunities exist.

“In this study, we focused on mainly embodied reductions for concrete,” explains Gregory. “But other construction materials could receive similar treatment.

“Further reductions could also come from retrofitting existing buildings and by designing structures with durability, hazard resilience, and adaptability in mind in order to minimize the need for reconstruction.”

This study answers a paradox in the field of sustainability. For the world to become more equitable, more development is necessary. And yet, that very same development may portend greater emissions.

The MIT team found that isn’t necessarily the case. Even as America continues to use more concrete, the benefits of the material itself and the interventions made to it can make climate targets more achievable.

The MIT Concrete Sustainability Hub is a team of researchers from several departments across MIT working on concrete and infrastructure science, engineering, and economics. Its research is supported by the Portland Cement Association and the Ready Mixed Concrete Research and Education Foundation.
',
 'Andrew Logan',
 'MIT News',
 'Intermediate', 0, NULL, '2026-03-18 10:04:00', '2026-03-18 10:04:00'),
 
(5, 'Civil Engineering',
 'Earthquake-Resistant Building Technology',
 'earthquake-resistant-building-technology',
 'After a large earthquake, the news inundates us with images of crumbled concrete, twisted steel, and disaster recovery teams searching through rubble for survivors. According to the California Department of Conservation, the 1989 Loma Prieta earthquake caused 63 deaths, and 3,757 people reported injuries from the disaster. The World Health Organization says that earthquakes caused nearly 750,000 deaths worldwide between 1998 and 2017. And more than 125 million people were affected, either through injuries or displacement.

Earthquakes themselves didn’t cause these deaths and injuries. Collapsed buildings, roads, and bridges were the greatest danger. As an industry adage says, earthquakes don’t kill people; buildings do.

Though earthquakes are uncontrollable, earthquake damage to people and property is predictable and preventable with earthquake engineering and earthquake-resistant building technology. While an earthquake-proof building is impossible, at least for the foreseeable future, earthquake resistance is possible with a holistic, cohesive approach.

Elements of an Earthquake-Resistant Structure
In the many parts of the world with frequent seismic activity, building earthquake-resistant structures is now common practice. While geophysicists and seismologists have made great advancements in early warning systems, they cannot yet predict exactly where, when, and how strongly an earthquake will strike. As the earth’s crust constantly changes, new seismic zones may emerge and long-existing zones may shift and change. Thus, all communities can benefit from knowledge of earthquake-resistant building technology.

Why Structures Fail in an Earthquake
Earthquakes occur when tectonic plates in the earth’s lithosphere (the mantle and crust) grind together and then suddenly shift. The shift produces a massive energy release that travels from the epicenter through the ground in concentric waves.

These waves then move through structures in both vertical and horizontal waves, stressing foundations, walls, and connections between materials. Most structures are designed to handle vertical forces, such as gravity and their own weight. They fail in an earthquake primarily because of the horizontal forces, which normal building codes don’t account for.

You can also view structural failure in terms of harmonics. All physical objects vibrate at a certain rate when force is applied, much like a tuning fork. When the vibration of seismic waves matches that of a structure’s harmonic frequency, the vibration is amplified. 

In earthquakes, some of the damage is immediate, catastrophic, and obvious. Other damage can be more insidious. For example, seismic vibration could separate roof flashing, the material that directs water away from vulnerable connection points in the roof. Then water can enter the structure (sometimes unnoticed) and cause damage later.

Fine cracks that can appear in columns or beams are another example. These cracks may not be apparent to the human eye, but they make their presence known when the next natural disaster strikes.

Methods for Earthquake Resistance
Methods for making a structure earthquake-resistant involve either deflecting, absorbing, transferring, or distributing vibrations from seismic activity. Those methods come into play with building design. A more holistic, proactive approach is seismic design. This process analyzes both the site and the surrounding area before building design begins.

In addition to analyzing the site’s geological features, other seismic hazards and other types of disaster are considered. For example, what communications technology or other utilities could be disrupted? How might nearby buildings impact or be impacted by the new building? Could nearby bodies of water cause flooding, either through a tsunami or a seiche?

All these considerations help establish priorities and inform which seismic resistance techniques to use. This holistic approach has the added benefit of hardening buildings against other threats, from terrorism to high-speed winds.

Earthquake Resistance Techniques to Protect Buildings and Inhabitants from Seismic Threats
Environmental monitoring and early-warning systems are continuously improving and may become the most effective way to protect buildings’ inhabitants from seismic threats. However, 5G deployment challenges continue to inhibit communications-related solutions in rural and low-income areas. Therefore, building earthquake-resistant structures remains paramount.

Making buildings resistant to earthquakes begins with the soil beneath it. Soft, silty soils are prone to liquefaction during earthquakes. Liquefaction is when soil temporarily behaves like a liquid. Soft soils can also amplify vibrations. Any structure on such soil is at risk. An earthquake-resistant building is best located on solid ground.

When existing structures aren’t located on solid ground, deep-mixing and compaction-grouting techniques can be applied to protect them from seismic threats. Compaction grouting involves adding cement-like materials to the soil around footings and pressurizing it. Deep mixing involves inserting diaphragms around the foundation and into an impervious layer and then pumping out any groundwater within the diaphragms.

Where to Employ Earthquake-Resistance Techniques within a Building
Earthquake-resistance techniques can be used throughout a building, from foundation to roof and exterior to interior. The specific technique depends on the type of vibration control ideal for that location.

A base isolator allows the foundation to move separately from the main building structure. This flexibility prevents most seismic vibrations from entering the structure.

Seismic dampers can be used throughout the foundation and structure to absorb vibrations from earthquake forces. Dampers come in a variety of forms. For example, viscous dampers use hydraulics to dissipate energy. A tuned mass damper uses weight at the top of or at critical points throughout a structure to counteract ground motion. Friction dampers are like the brakes in most cars, converting movement to heat. 

Structural reinforcements transfer or distribute vibrations to decrease their impact. For example, shear walls transfer vibrations to the foundation. Floors and roofs built as diaphragms distribute vibrations across the horizontal structure and into stronger vertical structures. Moment-resistant frames help connection points remain secure while allowing columns and beams to move without damage.

Nonstructural elements of the building can also cause significant injuries during an earthquake. In fact, a study in New Zealand showed that while failed structural elements caused the most fatalities, damaged nonstructural elements caused exponentially more injuries. The elements that caused the most injuries were furniture, shelving, suspended ceilings, and HVAC equipment and ducting.

The Federal Emergency Management Agency published an extensive guide on reducing risks of nonstructural earthquake damage. And cities in active seismic zones often have seismic building codes that address bracing guidelines for nonstructural elements.

New Building Materials for Earthquake-Resistant Construction
The best earthquake-resistant construction materials have an important quality in common: high ductility. Ductility refers to the material’s ability to move and change shape without breaking or losing strength. Traditionally, steel and wood are the best and most common earthquake-resistant materials.

Masonry and concrete have the lowest ductility. Unfortunately, many buildings erected prior to the 1950s used exactly those materials. Reinforcing or wrapping masonry and concrete can make such foundations and structures strong in an earthquake, which new materials are making increasingly possible.

New Materials
Scientists and engineers are developing new building materials for earthquake-resistant construction. These materials range from shape-memory alloys to invisibility cloaks to fibers created from synthetic spider silk.

Shape-memory alloys (SMAs) are fabricated metals that only change shape when cold and then return to their original shape when heated. “Cold” in this case could be as low as -100 degrees Celsius (-148 Fahrenheit). SMAs are highly ductile and create a damping effect due to their ability to dissipate heat.

Seismic invisibility cloaks are concentric rings of material surrounding a building’s foundation. These rings divert seismic waves around buildings. Scientists are still experimenting to find ideal materials (plastic, metal, trees, etc.) and configurations to create these rings. The drawback to this method is that it simply displaces vibrations instead of dissipating them. The risks to surrounding properties remain.

Spider silk is highly elastic yet stronger than steel. Its synthetic cousin displays similar properties, and manufacturers are racing to perfect it. The exact application in construction is yet to be determined. Theoretical construction-related applications include power grids, data networks, building cladding, scaffolding, and frames.

Technology-Based Techniques to Build Earthquake-Resistant Structures
New technology plays an important role in expanding our understanding of earthquakes and developing creative solutions to build earthquake-resistant structures. Seismic retrofitting, seismic analysis, and seismic sensors are aspects of this process.

The Importance of Seismic Retrofitting
According to researchers at Georgia Tech University, “nonductile concrete buildings are among the most common structures in the United States” and the most deadly. Older buildings may not have had the benefit of the seismic building codes at the time of their construction and thus require seismic retrofitting. Seismic retrofitting is extremely important to protect people and property in seismic zones.

This issue is about more than foundations, walls, and roofs. Consider utility lines (power, data, water, and gas) and how they might be impacted by an earthquake. If a building shifts, gas lines may separate and break, typically at connection points, but gas will continue to flow and fill the space. People in the space are then in danger of inhaling the gas, or the gas could ignite. Retrofitting gas lines may require multiple methods for maximum safety, including gas shut-off valves and flexible connections. 

Many of the techniques already mentioned can be applied to existing structures to protect them from seismic threats. The most common retrofits include strengthening connections between building elements, adding steel frames, and isolating the base. Renovations can include other new technology—such as energy and water efficiency, air quality control, a  , and fiber-optic cabling.

It’s also worth noting that not all buildings are good candidates for retrofitting. With the help of a seismic design, you may discover that it’s more cost effective to remove the old building and build a new one.

Seismic Analysis
Structural engineers use seismic analysis in earthquake engineering to predict how a structure will perform during seismic activity. While seismic building codes may specify which type of analysis is required in particular zones, engineers use a variety of models for full assessments.

Available models include equivalent static analysis, response spectrum analysis, linear dynamic analysis, nonlinear static analysis, and nonlinear dynamic analysis. Each type of analysis uses computer modeling for the complex calculations. With adequate seismic sensor data, artificial intelligence and machine learning can identify risks, structural faults, and even subtle fault lines that humans cannot.    

Seismic Sensors and Warning Systems
Monitoring seismic activity is important both to give structural engineers data about a site’s geological features and to improve early-warning systems. As mentioned previously, predicting earthquakes isn’t an exact science, but sending out alerts when an earthquake is happening is more feasible. Seconds make a difference in getting people to safety, particularly for those farther from the epicenter.

Ocean-based sensors can also detect underwater earthquakes to predict tsunamis and send alerts that include wave height and arrival time. When Tonga erupted in January 2022, sending a tsunami across the globe, the US National Oceanic Atmospheric Administration issued a tsunami advisory. Coastal areas received the information and were prepared well in advance of the wave’s arrival.

The phrase “seismic sensors” conjures an old pen-and-pendulum seismograph transcribing ground motion onto paper. However, seismic sensors have come a long way and diversified to sense different frequencies for different applications. Increasingly, these sensors are creating an Internet of Things network reliant on edge computing.

Edge computing, as opposed to cloud computing, brings data processing and storage physically closer to users to increase speed and decrease bandwidth use. The edge ecosystem requires robust, reliable internet service, as does sending out alerts and coordinating disaster recovery after an earthquake.

Holistic Earthquake Resistance
Earthquake resistance requires a holistic, cohesive approach that uses the latest trends in technology on multiple fronts. Earthquake-resistant building technology, seismic monitoring, early-warning systems, and natural disaster response all exist in the same system and should be treated accordingly. This approach will save lives and protect property.
',
 'IEEE Public Safety Technology Initiative',
 'IEEE Public Safety',
 'Advanced', 0, NULL, '2026-03-18 10:05:00', '2026-03-18 10:05:00'),
 
(6, 'Civil Engineering',
 'California''s Tallest Bridge Has Nothing Underneath',
 'californias-tallest-bridge-has-nothing-underneath',
 'Foresthill Bridge soars across the valley of the North Fork of the American River just outside Auburn, California. At more than 700 feet or 200 meters above the canyon floor, it’s the fourth-tallest bridge in the United States. When it opened in 1973, crowds cheered for the impressive new structure. But if you take a closer look, it doesn’t really make any sense.

This isn’t an interstate highway or even a major thoroughfare. The road sees only a few thousand vehicles a day, connecting Auburn, an exurb of Sacramento with a population just shy of 14,000, to scattered rural communities and recreation areas in the western foothills of the Sierra Nevadas. And while the American River does occasionally flood, it doesn’t flood 700 feet. Before this, the crossing was basically a low-water bridge.

A structure of this magnitude just looks out of place. But it wasn’t just a boondoggle, at least not at the outset. It was built that way for a reason, and the story behind it is not only pretty wild, but it also sits at the hinge point of a major chapter in American infrastructure. I’m Grady, and this is Practical Engineering.

California’s Central Valley is one of the world’s great agricultural regions: over 400 miles long, more than 50 miles wide, this remarkably fertile area is nearly half the size of England. The city of Sacramento sits near its center, right where the Sacramento and American Rivers meet.

To manage and distribute water across this enormous landscape, the federal government launched the Central Valley Project in 1933, a sweeping effort by the U.S. Bureau of Reclamation to store water in the wetter northern part of the valley and distribute it to the drier south. In the process, the system would also generate hydropower and reduce flood risk for growing urban centers. I’m glossing over a lot here. The history of California is steeped in water issues, and even just the Central Valley Project is nearly a century of details. But, critically, Folsom Dam was one of the first big components of the plan.

Built in 1955 on the American River, the concrete gravity dam provided significant flood protection to the City of Sacramento. However, it was constructed relatively early in our understanding of basin-scale hydrology and the uncertainty surrounding the frequency and magnitude of flooding over long periods of time. It became clear pretty quickly that Folsom Dam didn’t quite offer as much flood protection as was originally promised. Plus, because Folsom had to keep its flood pool empty to handle potential inflows, its ability to store water for irrigation or municipal supply purposes was somewhat limited.

The answer to these problems, at least according to the federal government, was Auburn Dam, authorized by Congress in 1968. The new structure would sit upstream of Folsom and control the variable flows of the North and Middle Forks of the American River. It would be the tallest dam in California and one of the tallest in the country. And work began in earnest in the early 1970s.

One of the first steps in the process was rerouting the American River. Crews built a large cofferdam and carved a diversion tunnel through the canyon wall. With the water redirected, they could begin drying out the bend in the river where the huge new dam would eventually sit.

Once the site was dried out, crews began exploring the underlying geology more thoroughly. They drilled boreholes, excavated tunnels and shafts, and surveyed the rock that would serve as the dam’s foundation. The site’s geology turned out to be more complex than expected. Some zones of rock were more compressible than others, which could lead to dangerous stress concentrations in the dam. And, there were a lot of joints and fissures in the rock mass, making it more challenging to predict how they would behave under extreme loads, in addition to creating paths for water. So the next phase of the project was a major foundation treatment program starting in 1974. This mainly involved pressure grouting fractures to reinforce weak zones against the enormous weight of the structure and to make the geology more watertight, preventing seepage from flowing under the dam.

With major construction works underway, anticipation for the reservoir was growing. Around the future rim, land values soared, and developers rushed to stake claims. Lakefront homes were planned. Entire communities emerged, built on the promise of a shining new shoreline. Then, in August 1975, a magnitude 5.9 earthquake struck near Oroville Dam, only about 50 miles or 80 kilometers away from the site.

The quake only caused minor damage to structures in the area, but it rattled confidence in the Auburn project. The geology of the western Sierra Nevadas had long been considered stable. But the Oroville earthquake introduced a troubling possibility: that the loading and filling of large reservoirs could trigger seismic events in the area. This phenomenon, known as reservoir-induced seismicity, is still not well understood even to this day. The pressure of water infiltrating bedrock and the weight of a reservoir can change the balance of forces along faults, potentially triggering movement. You know, when Oroville is full, that’s roughly 10 billion pounds of force or 4 billion kilograms of mass. It’s a staggering amount. You can imagine how that might affect the underlying geology.

The Auburn Dam, as a thin concrete arch, in contrast to the concrete gravity dam at Folsom or the earthfill embankment at Oroville, would be especially vulnerable to earthquakes. Thin-arch dams rely on the canyon walls to resist the thrust of the structure. In fact, I’ve made a video all about the topic you can check out after this! If one side shifts even a little during a quake, the results could be catastrophic. In April 1976, a report by the Association of Engineering Geologists concluded that an earthquake like the one at Oroville could cause the proposed Auburn Dam to catastrophically fail. It was back to the drawing board for the project, even as the foundation grouting program continued. And then the project was shaken again.

That same year, the newly completed Teton Dam in Idaho collapsed during its first filling, killing 11 people and causing billions in damage. It had been built by the same agency, the Bureau of Reclamation. Concern continued to mount about the safety of Auburn Dam, which would have catastrophic consequences for the thousands of Californians downstream if it were to fail. It was all enough to bring Auburn’s momentum to a halt.

While dam construction paused, one aspect of the project had already been finished: Foresthill Bridge. With a cofferdam on the river and the diversion tunnel only sized for smaller floods, there was a risk of overtopping the existing bridge, cutting off access between Auburn and the Sierra foothills. So, the Bureau of Reclamation decided to get a head start on a project that would eventually be inevitable: a new bridge, permanent and high enough to span the reservoir once it filled. If they were going to build a new bridge, they figured they might as well build it right the first time.

The result was a striking steel cantilever bridge with two slender concrete piers soaring skyward from the canyon floor. [Actually, there was another bridge planned over the Middle Fork of the American River - the Ruck-a-Chucky Bridge. It was a wild idea: a curved cable-stayed bridge where all the cables are anchored in the hillsides rather than tall towers. But while that project was shelved, Foresthill made it all the way through design and construction.] At the time of its opening in 1973, it was the second-highest bridge in the United States. But as time went on, it became increasingly clear they had jumped the gun.

By 1980, engineers floated two new dam designs that could withstand potential earthquakes. Both would be shifted slightly downstream from the original site. But by then, the tide of public and government support for the dam had turned.

Construction costs had ballooned, and Auburn Dam was looking less feasible every day. As originally proposed, the structure would be even larger than the Hoover Dam size, but store less than 10% of Lake Mead’s volume. Meanwhile, upgrades to Folsom Dam and improved levees around Sacramento offered far cheaper ways to reduce the flood risk that was the major impetus for the dam in the first place. New hydrologic data also suggested that earlier flow estimates had been overly optimistic, reducing its value for conservation. The benefits of Auburn Dam were shrinking as the costs grew. It was turning into an incredibly expensive solution in search of a problem.

At the same time, environmental and advocacy groups were gaining momentum. The project would flood canyons used for whitewater rafting and kayaking. It would drown ecosystems, inundate archaeological sites, and destroy long segments of the wild and scenic forks of the American River. It became clearer and clearer that the ends simply couldn’t justify the means. And yet, the idea never fully went away. 

In 1986, a massive flood hit the area. Water backed up at the diversion tunnel at Auburn, overtopped the cofferdam, and caused it to fail. Downstream levees were breached, and much of Sacramento flooded. For a moment, the momentum behind Auburn Dam and its promise of flood protection returned. But, it later became clear that the flood wasn’t entirely a natural disaster. The Bureau hadn’t followed the operating guidelines at Folsom Dam, worsening conditions downstream. And by then, grassroots opposition, cost concerns, and shifting priorities had all but put the Auburn Dam project to bed. Various proposals resurfaced over the years, including the idea of a “dry dam” that would only hold water during floods, but none gained much traction. With its many iterations and proposals, the project became known as the dam that wouldn’t die. But in 2008, the state of California revoked the Bureau’s water rights permit for the project, maybe not sealing its fate completely, but at least burying it several feet deeper. 

This story really gets to the heart of the challenge with large-scale public works projects. No matter how you configure them, there are big losers and big winners. There’s no doubt that a dam across the American River upstream of Folsom could provide significant benefits to the public: flood control, water supply, hydropower, recreational opportunities, or some combination of them all. But those benefits have to be weighed against real costs: environmental damage, staggering capital investment, long-term maintenance, the inherent risk of catastrophic failure, and the social toll of displacement and disruption. 

The mid-20th century was the heyday of American dam building, an era driven by ambition and optimism, but also by uncertainty. We didn’t have enough historical data to fully understand river systems. We couldn’t yet grasp the long-term consequences of altering them. And we couldn’t see into the future to know what the true impacts of these structures would be or what the cost of keeping them in good shape might amount to. 

Since then, we have a lot more experience with huge multi-purpose reservoirs. And it seems, in general, that the more we learn, the more the answer to whether they’re worth it seems to be: maybe not. And that maybe turns into a probably when you consider that all the best sites are already taken.

New Melones Dam, completed by the Bureau of Reclamation in 1979, not too far from Auburn, faced a lot of similar controversy and pushback. Although the project was eventually completed, the fight was bitter, and its legacy so far is mixed. The project is widely considered to be the last great American dam. At least, great in size, if not in public sentiment. No other reservoir of that scale has been built in the U.S. since. And with the Auburn Dam project mostly dead, it seems doubtful there ever will be.

The American River continued flowing through the diversion tunnel until 2007, when a new pump station and restoration project returned the river to its original channel. Kayakers can now navigate downstream, and even have some new features at the pump station to choose from: the artificial rapids on the left or the screen channel on the right. After more than three decades, the river was back in its place, tying a bow on a dam that was never built.

And yet, just a few miles upstream, the Foresthill Bridge still stands, dramatic, overbuilt, and strangely out of sync with its surroundings. And we’re still kind of stuck taking care of this bridge, whose scale is so out of proportion with its purpose. In the 2010s, the bridge underwent a major seismic retrofit to improve its safety and make future inspections easier. Most recently, it was part of a nationwide program inspecting bridges built with T-1 steel, an alloy that, in some cases, has shown concerning cracking at welds. The I-40 bridge crack in Memphis, which I covered in an earlier video, triggered the effort. And there have been quite a few defects found in bridges since then, so here’s hoping that Foresthill doesn’t make the list.

It’s a cool structure in its own right. But it stands for more than just an engineering achievement. Auburn Dam left a lot of scars, both on the physical landscape and the political one. But it also left this bridge that became more than just an out-of-place oddity. In a sense, it’s become a monument to the end of an era in US major public works projects, and, hopefully, a tribute to the caution and care that will shape the next one.
',
 'Wesley Crump',
 'Practical Engineering',
 'Advanced', 0, NULL, '2026-03-18 10:06:00', '2026-03-18 10:06:00');
 
-- ----------------------------------------
-- Subject 2: Mathematics (articles 7-12)
-- ----------------------------------------
INSERT INTO articles (article_id, subject, title, slug, content, author, source, level, read_count, deleted_at, created_at, updated_at) VALUES
(7, 'Mathematics',
 'Mathematician Solves Algebra''s Oldest Problem Using Intriguing New Number Sequences',
 'mathematician-solves-algebras-oldest-problem-new-number-sequences',
 'A UNSW Sydney mathematician has discovered a new method to tackle algebra's oldest challenge -- solving higher polynomial equations.

Polynomials are equations involving a variable raised to powers, such as the degree two polynomial: 1+ 4x -- 3x2 = 0.

The equations are fundamental to maths as well as science, where they have broad applications, like helping describe the movement of planets or writing computer programs.

However, a general method for solving 'higher order' polynomial equations, where x is raised to the power of five or higher, has historically proven elusive.

Now, UNSW Honorary Professor Norman Wildberger has revealed a new approach using novel number sequences, outlined in a recent publication with computer scientist Dr. Dean Rubine.

"Our solution reopens a previously closed book in mathematics history," Prof. Wildberger says.

The polynomial problem

Solutions to degree-two polynomials have been around since 1800 BC, thanks to the Babylonians' 'method of completing the square', which evolved into the quadratic formula familiar to many high school math students. This approach, using roots of numbers called 'radicals', was later extended to solve three- and four-degree polynomials in the 16th century.

Then, in 1832, French mathematician Évariste Galois showed how the mathematical symmetry behind the methods used to resolve lower-order polynomials became impossible for degree five and higher polynomials. Therefore, he figured, no general formula could solve them.

Approximate solutions for higher-degree polynomials have since been developed and are widely used in applications but, Prof. Wildberger says, these don't belong to pure algebra.

Radical rejection behind new method

The issue, he says, lies in the classical formula's use of third or fourth roots, which are radicals.

The radicals generally represent irrational numbers, which are decimals that extend to infinity without repeating and can't be written as simple fractions. For instance, the answer to the cubed root of seven, 3√7 = 1.9129118… extends forever.

Prof. Wildberger says this means that the real answer can never be completely calculated because "you would need an infinite amount of work and a hard drive larger than the universe."

So, when we assume 3√7 'exists' in a formula, we're assuming that this infinite, never-ending decimal is somehow a complete object.

This is why, Prof. Wildberger says, he "doesn't believe in irrational numbers."

Irrational numbers, he says, rely on an imprecise concept of infinity and lead to logical problems in mathematics.

Prof. Wildberger's rejection of radicals inspired his best-known contributions to mathematics, rational trigonometry and universal hyperbolic geometry. Both approaches rely on mathematical functions like squaring, adding, or multiplying, rather than irrational numbers, radicals, or functions like sine and cosine.

His new method to solve polynomials also avoids radicals and irrational numbers, relying instead on special extensions of polynomials called 'power series', which can have an infinite number of terms with the powers of x.

By truncating the power series, Prof. Wildberger says, they were able to extract approximate numerical answers to check that the method worked.

"One of the equations we tested was a famous cubic equation used by Wallis in the 17th century to demonstrate Newton's method. Our solution worked beautifully," he said.

New geometry for a general solution

However, Prof. Wildberger says the proof for the method is, ultimately, based on mathematical logic.

His method uses novel sequences of numbers that represent complex geometric relationships. These sequences belong to combinatorics, a branch of mathematics that deals with number patterns in sets of elements.

The most famous combinatorics sequence, called the Catalan numbers, describes the number of ways you can dissect a polygon, which is any shape with three or more sides, into triangles.

The numbers have important practical applications, including in computer algorithms, data structure designs, and game theory. They even appear in biology, where they're used to help count the possible folding patterns of RNA molecules. And they can be calculated using a simple two-degree polynomial.

"The Catalan numbers are understood to be intimately connected with the quadratic equation. Our innovation lies in the idea that if we want to solve higher equations, we should look for higher analogues of the Catalan numbers."

Prof. Wildberger's work extends these Catalan numbers from a one-dimensional to multi-dimensional array based on the number of ways a polygon can be divided using non-intersecting lines.

"We've found these extensions, and shown how, logically, they lead to a general solution to polynomial equations.

"This is a dramatic revision of a basic chapter in algebra."

Even quintics -- a degree five polynomial -- now have solutions, he says.

Aside from theoretical interest, he says, the method holds practical promise for creating computer programs that can solve equations using the algebraic series rather than radicals.

"This is a core computation for much of applied mathematics, so this is an opportunity for improving algorithms across a wide range of areas."

Geode's unexplored facets

Prof Wildberger says the novel array of numbers, which he and Dr. Rubine called the "Geode," also holds vast potential for further research.

"We introduce this fundamentally new array of numbers, the Geode, which extends the classical Catalan numbers and seem to underlie them.

"We expect that the study of this new Geode array will raise many new questions and keep combinatorialists busy for years.

"Really, there are so many other possibilities. This is only the start."
',
 'University of New South Wales',
 'ScienceDaily',
 'Easy', 0, NULL, '2026-03-18 10:07:00', '2026-03-18 10:07:00'),
 
(8, 'Mathematics',
 'A Short Introduction to the Work of James Maynard',
 'short-introduction-work-james-maynard',
 'James Maynard, a mathematician from the University of Oxford, has won one of this year's Fields Medals at the International Congress of Mathematicians. The Fields Medal is one of the most prestigious prizes in mathematics. It is awarded every four years "to recognise outstanding mathematical achievement for existing work and for the promise of future achievement". Up to four mathematicians up to the age of 40 are awarded a Fields Medal each time.

Primed for number theory
At the heart of number theory lie prime numbers - those numbers that are divisible only by themselves and 1. Because of this indivisibility they are often described as the atoms of number theory. Every other whole number can be "made" from these atoms in the sense that you can write it as a product of primes. The number 24, for example, can be written as

24 = 2 x 2 x 2 x 3,

and the number 110 can be written as

110 = 2 x 5 x 11.

In a similar way, every other whole number can be written as a product of primes.

The twin prime conjecture
We've known for thousands of years that there are infinitely many primes (see here for the simple and elegant proof) but there is no discernible pattern in how they are sprinkled along the number line. "Typically as you go down the number line the gaps between prime numbers get bigger," says Maynard. "But the [twin prime conjecture] is saying that even if the gaps get typically bigger, occasionally you get these gaps where the primes are very close together. Understanding the gaps between prime numbers is fundamental to understanding the distribution of the primes."

Apart from the number 2, all the other primes are odd numbers, so the closest two prime numbers could ever be (once you get past the number 2) is separated by a difference of two. At first it's easy to find these pairs of primes that are as close as can be, called twin primes: 3 and 5, 5 and 7, 11 and 13 all separated by 2. But this gets significantly harder the further you go up the number line. Mathematicians believe there are infinitely many pairs of twin primes which is known as the twin prime conjecture.

The twin prime conjecture is one of those famous problems in number theory that are simple to state and have fascinated mathematicians for hundreds of years and yet a proof still remains out of reach. After centuries of effort there was a big breakthrough in 2013, when Yitan Zhang proved that there were infinitely many pairs of primes that are separated by 70 million. "For mathematicians this was absolutely a huge breakthrough as this was the first time we had [a proof of gaps of] any finite number," says Maynard. "70 million is much bigger than 2 but it's a lot smaller than infinity."

Zhang's breakthrough involved sieve methods, which are ways of filtering numbers in your proof. "Sieve methods are a mathematical tool for translating some information you understand about numbers to create some information you'd like to know," says Maynard. Zhang's breakthrough involved proving very strong mathematical results that were needed as an input for the sieving method.

Maynard's approach was different: "Rather than improving the input to the method I changed the method itself. It became more efficient in turning one type of information into another and it meant that we needed much weaker inputs to get a result about boundary gaps between primes." With this new method he dramatically reduced the gap from 70 million to just 600. And after a flurry of collaborative work with a group of mathematicians, we now know there are infinitely many pairs of primes separated by a gap of just 246.

Even after such dramatic progress a proof of the twin prime conjecture still remains elusive. Work continues, but often involves taking new approaches. An example is Maynard's work proving there are infinitely many primes without certain digits. It's hard to know at this stage when the twin prime conjecture will finally be proved in full, but we came away from our discussion with Maynard feeling optimistic. "We're still one big idea away from proving the twin prime conjecture, but maybe we're only one big idea away."

Receiving one of the biggest prizes in mathematics is a huge honour, Maynard also finds it daunting, and slightly surreal. "In some ways it's intimidating thinking of my name on this list of legends of mathematics from the past. People I looked up to, when I was a kid and thinking about mathematicians," he says. " It's certainly quite surreal in that way."
',
 'Rachel Thomas',
 'Plus Magazine',
 'Easy', 0, NULL, '2026-03-18 10:08:00', '2026-03-18 10:08:00'),
 
(9, 'Mathematics',
 'The Math That Explains Why Bell Curves Are Everywhere',
 'the-math-that-explains-why-bell-curves-are-everywhere',
 'No matter where you look, a bell curve is close by.

Place a measuring cup in your backyard every time it rains and note the height of the water when it stops: Your data will conform to a bell curve. Record 100 people’s guesses at the number of jelly beans in a jar, and they’ll follow a bell curve. Measure enough women’s heights, men’s weights, SAT scores, marathon times — you’ll always get the same smooth, rounded hump that tapers at the edges.

Why does the bell curve pop up in so many datasets?

The answer boils down to the central limit theorem, a mathematical truth so powerful that it often strikes newcomers as impossible, like a magic trick of nature. “The central limit theorem is pretty amazing because it is so unintuitive and surprising,” said Daniela Witten(opens a new tab), a biostatistician at the University of Washington. Through it, the most random, unimaginable chaos can lead to striking predictability.

It’s now a pillar on which much of modern empirical science rests. Almost every time a scientist uses measurements to infer something about the world, the central limit theorem is buried somewhere in the methods. Without it, it would be hard for science to say anything, with any confidence, about anything.

“I don’t think the field of statistics would exist without the central limit theorem,” said Larry Wasserman(opens a new tab), a statistician at Carnegie Mellon University. “It’s everything.”

Purity From Vice
Perhaps it shouldn’t come as a surprise that the push to find regularity in randomness came from the study of gambling.

In the coffeehouses of early-18th-century London, Abraham de Moivre’s mathematical talents were obvious. Many of his contemporaries, including Isaac Newton and Edmond Halley, recognized his brilliance. De Moivre was a fellow of the Royal Society, but he was also a refugee, a Frenchman who had fled his home country as a young man in the face of anti-Protestant persecution. As a foreigner, he couldn’t secure the kind of steady academic post that would befit his talent. So to help pay his bills, he became a consultant to gamblers who sought a mathematical edge.

Flipping a coin, rolling a die, and drawing a card from a deck are random actions, with every outcome equally likely. What de Moivre realized is that when you combine many random actions, the result follows a reliable pattern.

Flip a coin 100 times and count how often it comes up heads. It’ll be somewhere around 50, but not very precisely. Play this game 10 times, and you may get 10 different counts.

Now imagine playing the game 1 million times. The bulk of the outcomes will be close to 50. You’ll almost never get under 10 heads or over 90. If you make a graph of how many times you see each number between zero and 100, you’ll see that classic bell shape, with 50 at the center. The more times you play the game, the smoother and clearer the bell will become.

De Moivre figured out the exact shape of this bell, which came to be called the normal distribution. It told him, without his having to actually play the game, how likely different outcomes were. For instance, the probability of getting between 45 and 55 heads is about 68%.

De Moivre marveled with religious devotion at the “steadfast order of the universe” that eventually overcame any and all deviations from the bell. “In process of time,” he wrote, “these irregularities will bear no proportion to the recurrency of that order which naturally results from original design.”

He used these insights to sustain a meager life in London, writing a book called The Doctrine of Chances that became a gambler’s bible, and holding informal office hours at the famed Old Slaughter’s Coffee House. But even de Moivre didn’t realize the full scope of his discovery. Only when Pierre-Simon Laplace ran with the idea in 1810, decades after de Moivre’s death, was its full reach uncovered.

Let’s take an example slightly more complex than coin flips: dice rolls. Every roll of a die has six equally likely outcomes. If you repeatedly roll the die and tally the results, you’ll get a chart that looks flat — you’re bound to see about as many rolls of 1 as you do 2 or 4 or 6.

Now roll that die 10 times and take the average. You’re likely to get somewhere around 3.5. Repeat the experiment many more times and graph up all the results. You’ll get a bell curve that peaks at 3.5, with a precisely defined structure on either side.

That’s the magic of the central limit theorem. You started with a distribution of possible outcomes that has no structure at all — equal chances of rolling 1 through 6. But by taking an average of multiple measurements, then repeating that process over and over, you get a precise, predictable, mathematical structure: the bell curve.

Laplace distilled this structure into a simple formula, the one that would later be known as the central limit theorem. No matter how irregular a random process is, even if it’s impossible to model, the average of many outcomes has the distribution that it describes. “It’s really powerful, because it means we don’t need to actually care what is the distribution of the things that got averaged,” Witten said. “All that matters is that the average itself is going to follow a normal distribution.”

An Omnipresent Tool
Averaging might seem like something it takes a human to do, but the central limit theorem applies invisibly to all kinds of things we can observe in the world, like human heights. “Somebody’s height might depend on their dad’s height, and their mom’s height, and their genetics, and their nutrition, and all these little effects that add up,” said Jeffrey Rosenthal(opens a new tab), a statistician at the University of Toronto. Those effects are unrelated to each other (generally, your dad’s height has nothing to do with the food you eat). “It’s kind of like averaging a bunch of little effects,” said Rosenthal, which is why height approximately follows a normal distribution.

This is why all kinds of datasets seem to conform to this beautiful shape spontaneously. “Anywhere that there’s an average under the hood, if it’s an average over enough things, then you’re going to end up with a normal distribution,” Witten said.

The theorem also gives statisticians the power to tell when something fishy is happening. Say you’re sipping coffee at Old Slaughter’s when a patron hands you a coin and bets that you can’t get 45 heads in 100 flips. You try, and only get 20. How can you tell whether he gave you a trick coin and the process is not as random as it ought to be? Thanks to the central limit theorem, you know that the numbers up to 20 only cover 0.15% of the bell, so there’s only a 0.15% chance a fair coin would give such a bad result. You’re almost certainly being had.

That’s the true power of Laplace’s formula. He knew that averaging over any process gives you a bell curve, which lets you say something about that process, without knowing anything deeper about how it works.

Handle With Care
Despite its centrality to modern science, the central limit theorem has limits of its own. It only works when you’re combining many samples, and those samples need to be independent. If they’re not — for example, if you only run a national presidential poll in a single small town in Maine — repeating the experiment won’t get you closer to the expected bell curve.

And sometimes in science, the outliers can be more important than the average. “The ‘hundred-year flood’ is suddenly happening more often,” said Richard D. De Veaux(opens a new tab), an applied statistician at Williams College. “These days, modeling extreme events is probably as important as modeling the mean.”

Fortunately, the idea behind the central limit theorem — the power and reliability of averages — has been used far and wide to extend the power of statistics. Statisticians often formulate a version of the central limit theorem for whatever specific problem they’re working on. “There’s so many more complicated things where if you’re clever you can write it as a sample mean plus some error,” Wasserman said. In those cases, you can use a variant of the theorem to simplify the problem.

The central limit theorem is a pillar of modern science, ultimately, because it’s a pillar of the world around us. When we combine lots of independent measurements, we get clusters. And if we’re clever enough, we can use those clusters to find out something interesting about the processes that made them.
',
 'Joseph Howlett',
 'Quanta Magazine',
 'Intermediate', 0, NULL, '2026-03-18 10:09:00', '2026-03-18 10:09:00'),
 
(10, 'Mathematics',
 'The Game Theory of How Algorithms Can Drive Up Prices',
 'game-theory-how-algorithms-can-drive-up-prices',
 'Imagine a town with two widget merchants. Customers prefer cheaper widgets, so the merchants must compete to set the lowest price. Unhappy with their meager profits, they meet one night in a smoke-filled tavern to discuss a secret plan: If they raise prices together instead of competing, they can both make more money. But that kind of intentional price-fixing, called collusion, has long been illegal. The widget merchants decide not to risk it, and everyone else gets to enjoy cheap widgets.

For well over a century, U.S. law has followed this basic template: Ban those backroom deals, and fair prices should be maintained. These days, it’s not so simple. Across broad swaths of the economy, sellers increasingly rely on computer programs called learning algorithms, which repeatedly adjust prices in response to new data about the state of the market. These are often much simpler than the “deep learning” algorithms that power modern artificial intelligence, but they can still be prone to unexpected behavior.

So how can regulators ensure that algorithms set fair prices? Their traditional approach won’t work, as it relies on finding explicit collusion. “The algorithms definitely are not having drinks with each other,” said Aaron Roth(opens a new tab), a computer scientist at the University of Pennsylvania.

Yet a widely cited 2019 paper(opens a new tab) showed that algorithms could learn to collude tacitly, even when they weren’t programmed to do so. A team of researchers pitted two copies of a simple learning algorithm against each other in a simulated market, then let them explore different strategies for increasing their profits. Over time, each algorithm learned through trial and error to retaliate when the other cut prices — dropping its own price by some huge, disproportionate amount. The end result was high prices, backed up by mutual threat of a price war.

Implicit threats like this also underpin many cases of human collusion. So if you want to guarantee fair prices, why not just require sellers to use algorithms that are inherently incapable of expressing threats?

In a recent paper(opens a new tab), Roth and four other computer scientists showed why this may not be enough. They proved that even seemingly benign algorithms that optimize for their own profit can sometimes yield bad outcomes for buyers. “You can still get high prices in ways that kind of look reasonable from the outside,” said Natalie Collina(opens a new tab), a graduate student working with Roth who co-authored the new study.

Researchers don’t all agree on the implications of the finding — a lot hinges on how you define “reasonable.” But it reveals how subtle the questions around algorithmic pricing can get, and how hard it may be to regulate.

“Without some notion of a threat or an agreement, it’s very hard for a regulator to come in and say, ‘These prices feel wrong,’” said Mallesh Pai(opens a new tab), an economist at Rice University. “That’s one reason why I think this paper is important.”

No Regrets
The recent paper studies algorithmic pricing through the lens of game theory, an interdisciplinary field at the border of economics and computer science that analyzes the mathematics of strategic competitions. It’s one way to explore the failures of pricing algorithms in a controlled setting.

“What we’re trying to do is create collusion in the lab,” said Joseph Harrington(opens a new tab), a University of Pennsylvania economist who wrote an influential review paper(opens a new tab) on regulating algorithmic collusion and was not involved in the new research. “Once we do so, we want to figure out how to destroy collusion.”

To understand the key ideas, it helps to start with the simple game of rock-paper-scissors. A learning algorithm, in this context, can be any strategy that a player uses to choose a move in each round based on data from previous rounds. Players might try out different strategies over the course of the game. But if they’re playing well, they’ll ultimately converge to a state that game theorists call equilibrium. In equilibrium, each player’s strategy is the best possible response to the other’s strategy, so neither player has an incentive to change.

In rock-paper-scissors, the ideal strategy is simple: You should play a random move each round, choosing all three possibilities equally often. Learning algorithms shine if one player takes a different approach. In that case, choosing moves based on previous rounds can help the other player win more often than if they just played randomly.

Suppose, for instance, that after many rounds you realize that your opponent, a geologist, chose rock more than 50% of the time. If you’d played paper every round, you would have won more often. Game theorists refer to this painful realization as regret.

Researchers have devised simple learning algorithms that are always guaranteed to leave you with zero regret. Slightly more sophisticated learning algorithms called “no-swap-regret” algorithms also guarantee that whatever your opponent did, you couldn’t have done better by swapping all instances of any move with any other move (say, by playing paper every time you actually played scissors). In 2000, game theorists proved(opens a new tab) that if you pit two no-swap-regret algorithms against each other in any game, they’ll end up in a specific kind of equilibrium — one that would be the optimal equilibrium if they only played a single round. That’s an attractive property, because single-round games are much simpler than multi-round ones. In particular, threats don’t work because players can’t follow through.

In a 2024 paper(opens a new tab), Jason Hartline(opens a new tab), a computer scientist at Northwestern University, and two graduate students translated the classic results from the 2000 paper to a model of a competitive market, where players can set new prices every round. In that context, the results implied that dueling no-swap-regret algorithms would always end up with competitive prices when they reached equilibrium. Collusion was impossible.

However, no-swap-regret algorithms aren’t the only pricing game strategies in the world of online marketplaces. So what happens when a no-swap-regret algorithm faces a different benign-looking opponent?

The Price Is Wrong
According to game theorists, the best strategy to play against a no-swap-regret algorithm is simple: Start with a specific probability for each possible move, and then choose one move at random every round, no matter what your opponent does. The ideal assignment of probabilities for this “nonresponsive” approach depends on the specific game you’re playing.

In the summer of 2024, Collina and her colleague Eshwar Arunachaleswaran(opens a new tab) set out to find those optimal probabilities for a two-player pricing game. They found that the best strategy assigned strikingly high probabilities to very high prices, along with lower probabilities for a wide range of lower prices. If you’re playing against a no-swap-regret algorithm, this strange strategy will maximize your profit. “To me, it was a complete surprise,” Arunachaleswaran said.

Nonresponsive strategies look superficially innocuous. They can’t convey threats, because they don’t react to their opponents’ moves at all. But they can coax learning algorithms to raise their prices, and then reap profits by occasionally undercutting their competitors.

At first, Collina and Arunachaleswaran thought that this artificial scenario wasn’t relevant to the real world. Surely the player using the no-swap-regret algorithm would switch to a different algorithm after realizing that their competitor was profiting at their expense.

But as they studied the problem further and discussed it with Roth and two other colleagues, they realized their intuition was wrong. The two players in their scenario were already in a state of equilibrium. Their profits were nearly equal, and both were as high as possible as long as neither player switched to a different algorithm. Neither player would have an incentive to change strategy, so buyers would be stuck with high prices. What’s more, the precise probabilities weren’t that important. Many different choices led to high prices when pitted against a no-swap-regret algorithm. It’s an outcome you’d expect from collusion, but without any collusive behavior in sight.

It Pays To Be Dumb
So, what can regulators do? Roth admits he doesn’t have an answer. It wouldn’t make sense to ban no-swap-regret algorithms: If everyone uses one, prices will fall. But a simple nonresponsive strategy might be a natural choice for a seller on an online marketplace like Amazon, even if it carries the risk of regret.

“One way to have regret is just to be kind of dumb,” Roth said. “Historically, that hasn’t been illegal.”

As Hartline sees it, the problem of algorithmic collusion has a simple solution: Ban all pricing algorithms except the no-swap-regret algorithms that game theorists have long favored. There may be practical ways to do this: In their 2024 work, Hartline and his colleagues devised a method for checking if an algorithm has a no-swap-regret property without looking at its code.

Hartline acknowledged that his preferred solution wouldn’t prevent all bad outcomes when no-swap-regret algorithms compete with humans. But he argued that scenarios like the one in Roth’s paper aren’t cases of algorithmic collusion.

“Collusion is a two-way thing,” he said. “It fundamentally must be the case that there are actions a single player can do to not collude.”

Either way, the new work still leaves many open questions about how algorithmic pricing can go wrong in the real world.

“We still don’t understand nearly as much as we want,” Pai said. “It’s an important question for our time.”
',
 'Ben Brubaker',
 'Quanta Magazine',
 'Intermediate', 0, NULL, '2026-03-18 10:10:00', '2026-03-18 10:10:00'),
 
(11, 'Mathematics',
 'At the Far Ends of a New Universal Law',
 'at-the-far-ends-of-a-new-universal-law',
 'Imagine an archipelago where each island hosts a single tortoise species and all the islands are connected — say by rafts of flotsam. As the tortoises interact by dipping into one another’s food supplies, their populations fluctuate.

In 1972, the biologist Robert May devised a simple mathematical model that worked much like the archipelago. He wanted to figure out whether a complex ecosystem can ever be stable or whether interactions between species inevitably lead some to wipe out others. By indexing chance interactions between species as random numbers in a matrix, he calculated(opens a new tab) the critical “interaction strength” — a measure of the number of flotsam rafts, for example — needed to destabilize the ecosystem. Below this critical point, all species maintained steady populations. Above it, the populations shot toward zero or infinity.

Little did May know, the tipping point he discovered was one of the first glimpses of a curiously pervasive statistical law.

The law appeared in full form two decades later, when the mathematicians Craig Tracy(opens a new tab) and Harold Widom(opens a new tab) proved that the critical point in the kind of model May used was the peak of a statistical distribution. Then, in 1999, Jinho Baik(opens a new tab), Percy Deift(opens a new tab) and Kurt Johansson(opens a new tab) discovered that the same statistical distribution also describes variations in sequences of shuffled integers — a completely unrelated mathematical abstraction. Soon the distribution appeared in models of the wriggling perimeter of a bacterial colony and other kinds of random growth. Before long, it was showing up all over physics and mathematics.

“The big question was why,” said Satya Majumdar(opens a new tab), a statistical physicist at the University of Paris-Sud. “Why does it pop up everywhere?”

Systems of many interacting components — be they species, integers or subatomic particles — kept producing the same statistical curve, which had become known as the Tracy-Widom distribution. This puzzling curve seemed to be the complex cousin of the familiar bell curve, or Gaussian distribution, which represents the natural variation of independent random variables like the heights of students in a classroom or their test scores. Like the Gaussian, the Tracy-Widom distribution exhibits “universality,” a mysterious phenomenon in which diverse microscopic effects give rise to the same collective behavior. “The surprise is it’s as universal as it is,” said Tracy, a professor at the University of California, Davis.

When uncovered, universal laws like the Tracy-Widom distribution enable researchers to accurately model complex systems whose inner workings they know little about, like financial markets, exotic phases of matter or the Internet.

“It’s not obvious that you could have a deep understanding of a very complicated system using a simple model with just a few ingredients,” said Grégory Schehr(opens a new tab), a statistical physicist who works with Majumdar at Paris-Sud. “Universality is the reason why theoretical physics is so successful.”

Universality is “an intriguing mystery,” said Terence Tao(opens a new tab), a mathematician at the University of California, Los Angeles who won the prestigious Fields Medal in 2006. Why do certain laws seem to emerge from complex systems, he asked, “almost regardless of the underlying mechanisms driving those systems at the microscopic level?”

Now, through the efforts of researchers like Majumdar and Schehr, a surprising explanation for the ubiquitous Tracy-Widom distribution is beginning to emerge.

Lopsided Curve
The Tracy-Widom distribution is an asymmetrical statistical bump, steeper on the left side than the right. Suitably scaled, its summit sits at a telltale value: √2N, the square root of twice the number of variables in the systems that give rise to it and the exact transition point between stability and instability that May calculated for his model ecosystem.

The transition point corresponded to a property of his matrix model called the “largest eigenvalue”: the greatest in a series of numbers calculated from the matrix’s rows and columns. Researchers had already discovered that the N eigenvalues of a “random matrix” — one filled with random numbers — tend to space apart along the real number line according to a distinct pattern, with the largest eigenvalue typically located at or near √2N. Tracy and Widom determined how the largest eigenvalues of random matrices fluctuate around this average value, piling up into the lopsided statistical distribution that bears their names.

When the Tracy-Widom distribution turned up in the integer sequences problem and other contexts that had nothing to do with random matrix theory, researchers began searching for the hidden thread tying all its manifestations together, just as mathematicians in the 18th and 19th centuries sought a theorem that would explain the ubiquity of the bell-shaped Gaussian distribution.

The central limit theorem, which was finally made rigorous about a century ago, certifies that test scores and other “uncorrelated” variables — meaning any of them can change without affecting the rest — will form a bell curve. By contrast, the Tracy-Widom curve appears to arise from variables that are strongly correlated, such as interacting species, stock prices and matrix eigenvalues. The feedback loop of mutual effects between correlated variables makes their collective behavior more complicated than that of uncorrelated variables like test scores. While researchers have rigorously proved(opens a new tab) certain classes of random matrices in which the Tracy-Widom distribution universally holds, they have a looser handle on its manifestations in counting problems, random-walk problems, growth models and beyond.

“No one really knows what you need in order to get Tracy-Widom,” said Herbert Spohn(opens a new tab), a mathematical physicist at the Technical University of Munich in Germany. “The best we can do,” he said, is to gradually uncover the range of its universality by tweaking systems that exhibit the distribution and seeing whether the variants give rise to it too.

So far, researchers have characterized three forms of the Tracy-Widom distribution: rescaled versions of one another that describe strongly correlated systems with different types of inherent randomness. But there could be many more than three, perhaps even an infinite number, of Tracy-Widom universality classes. “The big goal is to find the scope of universality of the Tracy-Widom distribution,” said Baik, a professor of mathematics at the University of Michigan. “How many distributions are there? Which cases give rise to which ones?”

As other researchers identified further examples of the Tracy-Widom peak, Majumdar, Schehr and their collaborators began hunting for clues in the curve’s left and right tails.

Going Through a Phase
Majumdar became interested in the problem in 2006 during a workshop at the University of Cambridge in England. He met a pair of physicists who were using random matrices to model string theory’s abstract space of all possible universes. The string theorists reasoned(opens a new tab) that stable points in this “landscape” corresponded to the subset of random matrices whose largest eigenvalues were negative — far to the left of the average value of √2N at the peak of the Tracy-Widom curve. They wondered just how rare these stable points — the seeds of viable universes — might be.

To answer the question, Majumdar and David Dean(opens a new tab), now of the University of Bordeaux in France, realized that they needed to derive an equation describing the tail to the extreme left of the Tracy-Widom peak, a region of the statistical distribution that had never been studied. Within a year, their derivation of the left “large deviation function” appeared in Physical Review Letters(opens a new tab). Using different techniques, Majumdar and Massimo Vergassola(opens a new tab) of Pasteur Institute in Paris calculated the right large deviation function three years later. On the right, Majumdar and Dean were surprised to find that the distribution dropped off at a rate related to the number of eigenvalues, N; on the left, it tapered off more quickly, as a function of N2.

In 2011, the form of the left and right tails gave Majumdar, Schehr and Peter Forrester(opens a new tab) of the University of Melbourne in Australia a flash of insight: They realized the universality of the Tracy-Widom distribution could be related to the universality of phase transitions — events such as water freezing into ice, graphite becoming diamond and ordinary metals transforming into strange superconductors.

Because phase transitions are so widespread — all substances change phases when fed or starved of sufficient energy — and take only a handful of mathematical forms, they are for statistical physicists “almost like a religion,” Majumdar said.

In the miniscule margins of the Tracy-Widom distribution, Majumdar, Schehr and Forrester recognized familiar mathematical forms: distinct curves describing two different rates of change in the properties of a system, sloping downward from either side of a transitional peak. These were the trappings of a phase transition.

In the thermodynamic equations describing water, the curve that represents the water’s energy as a function of temperature has a kink at 100 degrees Celsius, the point at which the liquid becomes steam. The water’s energy slowly increases up to this point, suddenly jumps to a new level and then slowly increases again along a different curve, in the form of steam. Crucially, where the energy curve has a kink, the “first derivative” of the curve — another curve that shows how quickly the energy changes at each point — has a peak.

Similarly, the physicists realized, the energy curves of certain strongly correlated systems have a kink at √2N. The associated peak for these systems is the Tracy-Widom distribution, which appears in the third derivative of the energy curve — that is, the rate of change of the rate of change of the energy’s rate of change. This makes the Tracy-Widom distribution a “third-order” phase transition.

“The fact that it pops up everywhere is related to the universal character of phase transitions,” Schehr said. “This phase transition is universal in the sense that it does not depend too much on the microscopic details of your system.”

According to the form of the tails, the phase transition separated phases of systems whose energy scaled with N2 on the left and N on the right. But Majumdar and Schehr wondered what characterized this Tracy-Widom universality class; why did third-order phase transitions always seem to occur in systems of correlated variables?

The answer lay buried in a pair of esoteric papers from 1980. A third-order phase transition had shown up before, identified that year in a simplified version of the theory governing atomic nuclei. The theoretical physicists David Gross, Edward Witten and (independently) Spenta Wadia discovered(opens a new tab) a third-order phase transition separating a “weak coupling” phase, in which matter takes the form of nuclear particles, and a higher-temperature “strong coupling” phase, in which matter melds into plasma. After the Big Bang, the universe probably transitioned from a strong- to a weak-coupling phase as it cooled.

After examining the literature, Schehr said, he and Majumdar “realized there was a deep connection between our probability problem and this third-order phase transition that people had found in a completely different context.”

Weak to Strong
Majumdar and Schehr have since accrued substantial evidence(opens a new tab) that the Tracy-Widom distribution and its large deviation tails represent a universal phase transition between weak- and strong-coupling phases. In May’s ecosystem model, for example, the critical point at √2N separates a stable phase of weakly coupled species, whose populations can fluctuate individually without affecting the rest, from an unstable phase of strongly coupled species, in which fluctuations cascade through the ecosystem and throw it off balance. In general, Majumdar and Schehr believe, systems in the Tracy-Widom universality class exhibit one phase in which all components act in concert and another phase in which the components act alone.

The asymmetry of the statistical curve reflects the nature of the two phases. Because of mutual interactions between the components, the energy of the system in the strong-coupling phase on the left is proportional to N2. Meanwhile, in the weak-coupling phase on the right, the energy depends only on the number of individual components, N.

“Whenever you have a strongly coupled phase and a weakly coupled phase, Tracy-Widom is the connecting crossover function between the two phases,” Majumdar said.

Majumdar and Schehr’s work is “a very nice contribution,” said Pierre Le Doussal(opens a new tab), a physicist at École Normale Supérieure in France who helped prove the presence of the Tracy-Widom distribution(opens a new tab) in a stochastic growth model called the KPZ equation. Rather than focusing on the peak of the Tracy-Widom distribution, “the phase transition is probably the deeper level” of explanation, Le Doussal said. “It should basically make us think more about trying to classify these third-order transitions.”

Leo Kadanoff(opens a new tab), the statistical physicist who introduced the term “universality” and helped classify universal phase transitions in the 1960s, said it has long been clear to him that universality in random matrix theory must somehow be connected to the universality of phase transitions. But while the physical equations describing phase transitions seem to match reality, many of the computational methods used to derive them have never been made mathematically rigorous.

“Physicists will, in a pinch, settle for a comparison with nature,” Kadanoff said, “Mathematicians want proofs — proof that phase-transition theory is correct; more detailed proofs that random matrices fall into the universality class of third-order phase transitions; proof that such a class exists.”

For the physicists involved, a preponderance of evidence will suffice. The task now is to identify and characterize strong- and weak-coupling phases in more of the systems that exhibit the Tracy-Widom distribution, such as growth models, and to predict and study new examples of Tracy-Widom universality throughout nature.

The telltale sign will be the tails of the statistical curves. At a gathering of experts in Kyoto, Japan, in August, Le Doussal encountered Kazumasa Takeuchi, a University of Tokyo physicist who reported in 2010(opens a new tab) that the interface between two phases of a liquid crystal material varies according to the Tracy-Widom distribution. Four years ago, Takeuchi had not collected enough data to plot extreme statistical outliers, such as prominent spikes along the interface. But when Le Doussal entreated Takeuchi to plot the data again, the scientists saw the first glimpse of the left and right tails. Le Doussal immediately emailed Majumdar with the news.

“Everybody looks only at the Tracy-Widom peak,” Majumdar said. “They don’t look at the tails because they are very, very tiny things.”
',
 'Natalie Wolchover',
 'Quanta Magazine',
 'Advanced', 0, NULL, '2026-03-18 10:11:00', '2026-03-18 10:11:00'),
 
(12, 'Mathematics',
 'In Mathematics, You Cannot Be Lied To',
 'in-mathematics-you-cannot-be-lied-to',
 'A few years back, a prospective doctoral student sought out Sylvia Serfaty(opens a new tab) with some existential questions about the apparent uselessness of pure math. Serfaty, then newly decorated with the prestigious Henri Poincaré Prize, won him over simply by being honest and nice. “She was very warm and understanding and human,” said Thomas Leblé, now an instructor at the Courant Institute of Mathematical Sciences at New York University. “She made me feel that even if at times it might seem futile, at least it would be friendly. The intellectual and human adventure would be worth it.” For Serfaty, mathematics is about building scientific and human connections. But as Leblé recalled, Serfaty also emphasized that a mathematician has to find satisfaction in “weaving one’s own rug,” alluding to the patient, solitary work that comes first.

Born and raised in Paris, Serfaty first became intrigued by mathematics in high school. Ultimately she gravitated toward physics problems, constructing mathematical tools to forecast what should happen in physical systems. For her doctoral research in the late-1990s, she focused on the Ginzburg-Landau equations, which describe superconductors and their vortices that turn like little whirlwinds. The problem she tackled was to determine when, where and how the vortices appear in the static (time-independent) ground state. She solved this problem with increasing detail over the course of more than a decade, together with Étienne Sandier of the University of Paris-East, with whom she co-authored the book Vortices in the Magnetic Ginzburg-Landau Model.

In 1998, Serfaty discovered an irresistibly puzzling problem about how these vortices evolve in time. She decided that this was the problem she really wanted to solve. Thinking about it initially, she got stuck and abandoned it, but now and then she circled back. For years, with collaborators, she built tools that she hoped might eventually provide pathways to the desired destination. In 2015, after almost 18 years, she finally hit upon the right point of view and arrived at the solution.

“First you start from a vision that something should be true,” Serfaty said. “I think we have software, so to speak, in our brain that allows us to judge that moral quality, that truthful quality to a statement.”

And, she noted, “you cannot be cheated, you cannot be lied to. A thing is true or not true, and there is this notion of clarity on which you can base yourself.”
In 2004, at age 28, she won the European Mathematical Society prize for her work analyzing the Ginzburg-Landau model; this was followed by the Poincaré Prize in 2012. Last September, the piano-playing, bicycle-riding mother of two returned as a fulltime faculty member to the Courant Institute, where she had held various positions since 2001. By her count, she is one of five women among about 60 full-time faculty members in the math department, a ratio she figures is unlikely to balance itself out anytime soon.

Quanta Magazine talked with Serfaty in January at the Courant Institute. An edited and condensed version of the conversation follows.

QUANTA MAGAZINE: When did you find mathematics?
SYLVIA SERFATY: In high school, there was one episode that crystallized it for me: We had assignments, little problems to solve at home, and one of them seemed very difficult. I had been thinking about it and thinking about it, and wandering around trying to find a solution. And in the end I came up with a solution that was not the one that was expected — it was more general than the problem was calling for, making it more abstract. So when the teacher gave the solutions, I proposed mine as an alternative, and I think everybody was surprised, including the teacher herself.

I was happy that I’d found a creative solution. I was a teenager, and a little bit idealistic. I wanted to have a creative impact, and research seemed like a beautiful profession. I knew I was not an artist. My dad is an architect and he’s really an artist, in the full sense of the word. I always compared myself to that image: the guy who has talent, has a gift. That played a role in building my self-perception of what I could do and what I wanted to achieve.

So you don’t think of yourself as having a gift — you weren’t a prodigy.
No. We do a disservice to the profession by giving this image of little geniuses and prodigies. These Hollywood movies about scientists can be somewhat counterproductive, too. They are telling children that there are geniuses out there that do really cool stuff, and kids may think, “Oh, that’s not me.” Maybe 5 percent of the profession fits that stereotype, but 95 percent doesn’t. You don’t have to be among the 5 percent to do interesting math.

For me, it took a lot of faith and believing in my little dream. My parents told me, “You can do anything, you should go for it” — my mother is a teacher and she always told me I was at the top of my cohort and that if I didn’t succeed, who will? My first university math teacher played a big role and really believed in my potential, and then as I pursued my studies, my intuition was confirmed that I really liked math — I liked the beauty of it, and I liked the challenge.

So you have to be comfortable with frustration if you want to be a mathematician?
That’s research. You enjoy solving a problem if you have difficulty solving it. The fun is in the struggle with a problem that resists. It’s the same kind of pleasure as with hiking: You hike uphill and it’s tough and you sweat, and at the end of the day the reward is the beautiful view. Solving a math problem is a bit like that, but you don’t always know where the path is and how far you are from the top. You have to be able to accept frustration, failure, your own limitations. Of course you have to be good enough; that’s a minimum requirement. But if you have enough ability, then you cultivate it and build on it, just as a musician plays scales and practices to get to a top level.

How do you tackle a problem?
One of the first pieces of advice I got as I was starting my Ph.D. was from Tristan Rivière (a previous student of my adviser, Fabrice Béthuel), who told me: People think that research in math is about these big ideas, but no, you really have to start from simple, stupid computations — start again like a student and redo everything yourself. I found that this is so true. A lot of good research actually starts from very simple things, elementary facts, basic bricks, from which you can build a big cathedral. Progress in math comes from understanding the model case, the simplest instance in which you encounter the problem. And often it is an easy computation; it’s just that no one had thought of looking at it this way.

Do you cultivate that perspective, or does it come naturally?
This is all I know how to do. I tell myself that there are always very bright people who have thought about these problems and made very beautiful and elaborate theories, and certainly I cannot always compete on that end. But let me try to rethink the problem almost from scratch with my own little basic understanding and knowledge and see where I go. Of course, I have built enough experience and intuition that I sort of pretend to be naive. In the end, I think a lot of mathematicians proceed this way, but maybe they don’t want to admit it, because they don’t want to appear simple-minded. There is a lot of ego in this profession, let’s be honest.

Does the ego help or hinder mathematical ambition?
We do math research because we like the problems, and we enjoy finding solutions, but I think maybe half of it is because we want to impress others. Would you do math if you were on a desert island and there was no one to admire your beautiful proof? We prove theorems because there is an audience to communicate it to. A lot of the motivation is presenting the work at the next conference and seeing what colleagues think. And then people appreciate it and provide positive feedback, and this feeds the motivation. And then you may get prizes, and if so, maybe you get even more prizes because you already have prizes. And you get published in good journals, and you keep track of how many papers you published and how many citations you got on MathSciNet, and you inevitably get in the habit of sometimes comparing yourself to your friends. You are constantly judged by your peers.

This is a system that increases people’s productivity. It works very well to push people to publish and to work, because they want to maintain their ranking. But it also puts a lot of ego into it. And at some point I think it’s too much. We need to put more focus on the real scientific progress, rather than on the signs of wealth, so to speak. And I certainly think this aspect is not very female-friendly. There’s also the nerd stereotype — I don’t think of myself as a nerd. I don’t identify with that culture. And I don’t think that because I’m a mathematician I have to be a nerd.

Would more women in the field help shift the balance?
I’m not super-optimistic, in terms of women in the field. I don’t think it’s a problem that is going to naturally resolve itself. The numbers over the last 20 years are not a great improvement, sometimes even decreasing.

The question is: Can you convince men that it would really be better for science and math if there were more women around? I’m not sure they are all convinced. Would it be better? Why? Would it make their life better, would it make the math better? I tend to think it would be better.

In what way?
It’s good to have a diversity of frames of mind. Two different mathematicians think in two slightly different ways, and women do tend to think a little bit differently. Math is not about everybody staring at a problem and trying to solve it. We don’t even know where the problems are. Some people decide they are going to explore over here, and some people explore over there. That’s why you need people with different points of view, to think of different perspectives and find different roads.

In your own work over the past two decades, you’ve specialized in one area of mathematical physics, but this has led you in a variety of directions.
It’s really beautiful to observe, as you progress in your mathematical maturity, how everything is somehow connected. There are so many things that are related, and you keep building connections in your intellectual landscape. With experience you develop a point of view that is pretty much unique to yourself — somebody else would come at it from a different angle. That’s what’s fruitful, and that’s how you can solve problems that maybe somebody smarter than you wouldn’t solve just because they don’t have the necessary perspective.

And your approach has unexpectedly opened doors to other fields — how did that come about?
One important question I had from the beginning was to understand the patterns of the vortices. Physicists knew from experiments that the vortices form triangular lattices, called Abrikosov lattices, and so the question was to prove why they form these patterns. This we never completely answered, but we have made progress. A paper we published in 2012(opens a new tab) rigorously connected the Ginzburg-Landau problem of vortices with a crystallization problem for the first time. And this problem, as it turns out, arises in other areas of math, such as number theory and statistical mechanics and random matrices.

What we proved was that the vortices in the superconductor behave like particles with what’s called a Coulomb interaction — essentially, the vortices act like electric charges and repel each other. You can think of the particles as people who don’t like each other but are forced to stay in the same room — where should they stand to minimize their repulsion to others?

Was it difficult to cross over into a new area?
It was a challenge, because I had to learn the basics of a new subject area and nobody knew me in that field. And initially there was some skepticism about our results. But arriving as newcomers allowed us to develop a new point of view because we weren’t burdened by any preconceived notions — ignorance is helpful in this instance.

Some mathematicians, they start with something, they know how to do it, and then they create variants, like derivative products: You make the film and then you sell the T-shirts, and then you sell the mugs. I think the way that you can distinguish good mathematicians is that they are constantly moving further and forward and advancing onto new ground.
',
 'Siobhan Roberts',
 'Quanta Magazine',
 'Advanced', 0, NULL, '2026-03-18 10:12:00', '2026-03-18 10:12:00');
 
-- ----------------------------------------
-- Subject 3: Computer Science (articles 13-18)
-- ----------------------------------------
INSERT INTO articles (article_id, subject, title, slug, content, author, source, level, read_count, deleted_at, created_at, updated_at) VALUES
(13, 'Computer Science',
 'Explained: Generative AI',
 'explained-generative-ai',
 'PA quick scan of the headlines makes it seem like generative artificial intelligence is everywhere these days. In fact, some of those headlines may actually have been written by generative AI, like OpenAI’s ChatGPT, a chatbot that has demonstrated an uncanny ability to produce text that seems to have been written by a human.

But what do people really mean when they say “generative AI?”

Before the generative AI boom of the past few years, when people talked about AI, typically they were talking about machine-learning models that can learn to make a prediction based on data. For instance, such models are trained, using millions of examples, to predict whether a certain X-ray shows signs of a tumor or if a particular borrower is likely to default on a loan.

Generative AI can be thought of as a machine-learning model that is trained to create new data, rather than making a prediction about a specific dataset. A generative AI system is one that learns to generate more objects that look like the data it was trained on.

“When it comes to the actual machinery underlying generative AI and other types of AI, the distinctions can be a little bit blurry. Oftentimes, the same algorithms can be used for both,” says Phillip Isola, an associate professor of electrical engineering and computer science at MIT, and a member of the Computer Science and Artificial Intelligence Laboratory (CSAIL).

And despite the hype that came with the release of ChatGPT and its counterparts, the technology itself isn’t brand new. These powerful machine-learning models draw on research and computational advances that go back more than 50 years.

An increase in complexity

An early example of generative AI is a much simpler model known as a Markov chain. The technique is named for Andrey Markov, a Russian mathematician who in 1906 introduced this statistical method to model the behavior of random processes. In machine learning, Markov models have long been used for next-word prediction tasks, like the autocomplete function in an email program.

In text prediction, a Markov model generates the next word in a sentence by looking at the previous word or a few previous words. But because these simple models can only look back that far, they aren’t good at generating plausible text, says Tommi Jaakkola, the Thomas Siebel Professor of Electrical Engineering and Computer Science at MIT, who is also a member of CSAIL and the Institute for Data, Systems, and Society (IDSS).

“We were generating things way before the last decade, but the major distinction here is in terms of the complexity of objects we can generate and the scale at which we can train these models,” he explains.

Just a few years ago, researchers tended to focus on finding a machine-learning algorithm that makes the best use of a specific dataset. But that focus has shifted a bit, and many researchers are now using larger datasets, perhaps with hundreds of millions or even billions of data points, to train models that can achieve impressive results.

The base models underlying ChatGPT and similar systems work in much the same way as a Markov model. But one big difference is that ChatGPT is far larger and more complex, with billions of parameters. And it has been trained on an enormous amount of data — in this case, much of the publicly available text on the internet.

In this huge corpus of text, words and sentences appear in sequences with certain dependencies. This recurrence helps the model understand how to cut text into statistical chunks that have some predictability. It learns the patterns of these blocks of text and uses this knowledge to propose what might come next.

More powerful architectures

While bigger datasets are one catalyst that led to the generative AI boom, a variety of major research advances also led to more complex deep-learning architectures.

In 2014, a machine-learning architecture known as a generative adversarial network (GAN) was proposed by researchers at the University of Montreal. GANs use two models that work in tandem: One learns to generate a target output (like an image) and the other learns to discriminate true data from the generator’s output. The generator tries to fool the discriminator, and in the process learns to make more realistic outputs. The image generator StyleGAN is based on these types of models.  

Diffusion models were introduced a year later by researchers at Stanford University and the University of California at Berkeley. By iteratively refining their output, these models learn to generate new data samples that resemble samples in a training dataset, and have been used to create realistic-looking images. A diffusion model is at the heart of the text-to-image generation system Stable Diffusion.

In 2017, researchers at Google introduced the transformer architecture, which has been used to develop large language models, like those that power ChatGPT. In natural language processing, a transformer encodes each word in a corpus of text as a token and then generates an attention map, which captures each token’s relationships with all other tokens. This attention map helps the transformer understand context when it generates new text.

These are only a few of many approaches that can be used for generative AI.

A range of applications

What all of these approaches have in common is that they convert inputs into a set of tokens, which are numerical representations of chunks of data. As long as your data can be converted into this standard, token format, then in theory, you could apply these methods to generate new data that look similar.

“Your mileage might vary, depending on how noisy your data are and how difficult the signal is to extract, but it is really getting closer to the way a general-purpose CPU can take in any kind of data and start processing it in a unified way,” Isola says.

This opens up a huge array of applications for generative AI.

For instance, Isola’s group is using generative AI to create synthetic image data that could be used to train another intelligent system, such as by teaching a computer vision model how to recognize objects.

Jaakkola’s group is using generative AI to design novel protein structures or valid crystal structures that specify new materials. The same way a generative model learns the dependencies of language, if it’s shown crystal structures instead, it can learn the relationships that make structures stable and realizable, he explains.

But while generative models can achieve incredible results, they aren’t the best choice for all types of data. For tasks that involve making predictions on structured data, like the tabular data in a spreadsheet, generative AI models tend to be outperformed by traditional machine-learning methods, says Devavrat Shah, the Andrew and Erna Viterbi Professor in Electrical Engineering and Computer Science at MIT and a member of IDSS and of the Laboratory for Information and Decision Systems.

“The highest value they have, in my mind, is to become this terrific interface to machines that are human friendly. Previously, humans had to talk to machines in the language of machines to make things happen. Now, this interface has figured out how to talk to both humans and machines,” says Shah.

Raising red flags

Generative AI chatbots are now being used in call centers to field questions from human customers, but this application underscores one potential red flag of implementing these models — worker displacement.

In addition, generative AI can inherit and proliferate biases that exist in training data, or amplify hate speech and false statements. The models have the capacity to plagiarize, and can generate content that looks like it was produced by a specific human creator, raising potential copyright issues.

On the other side, Shah proposes that generative AI could empower artists, who could use generative tools to help them make creative content they might not otherwise have the means to produce.

In the future, he sees generative AI changing the economics in many disciplines.

One promising future direction Isola sees for generative AI is its use for fabrication. Instead of having a model make an image of a chair, perhaps it could generate a plan for a chair that could be produced.

He also sees future uses for generative AI systems in developing more generally intelligent AI agents.

“There are differences in how these models work and how we think the human brain works, but I think there are also similarities. We have the ability to think and dream in our heads, to come up with interesting ideas or plans, and I think generative AI is one of the tools that will empower agents to do that, as well,” Isola says.
',
 'Adam Zewe',
 'MIT News',
 'Easy', 0, NULL, '2026-03-18 10:13:00', '2026-03-18 10:13:00'),
 
(14, 'Computer Science',
 'Robotic System Zeroes in on Objects Most Relevant for Helping Humans',
 'robotic-system-zeroes-in-objects-most-relevant-helping-humans',
 'For a robot, the real world is a lot to take in. Making sense of every data point in a scene can take a huge amount of computational effort and time. Using that information to then decide how to best help a human is an even thornier exercise.

Now, MIT roboticists have a way to cut through the data noise, to help robots focus on the features in a scene that are most relevant for assisting humans.

Their approach, which they aptly dub “Relevance,” enables a robot to use cues in a scene, such as audio and visual information, to determine a human’s objective and then quickly identify the objects that are most likely to be relevant in fulfilling that objective. The robot then carries out a set of maneuvers to safely offer the relevant objects or actions to the human.

The researchers demonstrated the approach with an experiment that simulated a conference breakfast buffet. They set up a table with various fruits, drinks, snacks, and tableware, along with a robotic arm outfitted with a microphone and camera. Applying the new Relevance approach, they showed that the robot was able to correctly identify a human’s objective and appropriately assist them in different scenarios.

In one case, the robot took in visual cues of a human reaching for a can of prepared coffee, and quickly handed the person milk and a stir stick. In another scenario, the robot picked up on a conversation between two people talking about coffee, and offered them a can of coffee and creamer.

Overall, the robot was able to predict a human’s objective with 90 percent accuracy and to identify relevant objects with 96 percent accuracy. The method also improved a robot’s safety, reducing the number of collisions by more than 60 percent, compared to carrying out the same tasks without applying the new method.

“This approach of enabling relevance could make it much easier for a robot to interact with humans,” says Kamal Youcef-Toumi, professor of mechanical engineering at MIT. “A robot wouldn’t have to ask a human so many questions about what they need. It would just actively take information from the scene to figure out how to help.”

Youcef-Toumi’s group is exploring how robots programmed with Relevance can help in smart manufacturing and warehouse settings, where they envision robots working alongside and intuitively assisting humans.

Youcef-Toumi, along with graduate students Xiaotong Zhang and Dingcheng Huang, will present their new method at the IEEE International Conference on Robotics and Automation (ICRA) in May. The work builds on another paper presented at ICRA the previous year.

Finding focus

The team’s approach is inspired by our own ability to gauge what’s relevant in daily life. Humans can filter out distractions and focus on what’s important, thanks to a region of the brain known as the Reticular Activating System (RAS). The RAS is a bundle of neurons in the brainstem that acts subconsciously to prune away unnecessary stimuli, so that a person can consciously perceive the relevant stimuli. The RAS helps to prevent sensory overload, keeping us, for example, from fixating on every single item on a kitchen counter, and instead helping us to focus on pouring a cup of coffee.

“The amazing thing is, these groups of neurons filter everything that is not important, and then it has the brain focus on what is relevant at the time,” Youcef-Toumi explains. “That’s basically what our proposition is.”

He and his team developed a robotic system that broadly mimics the RAS’s ability to selectively process and filter information. The approach consists of four main phases. The first is a watch-and-learn “perception” stage, during which a robot takes in audio and visual cues, for instance from a microphone and camera, that are continuously fed into an AI “toolkit.” This toolkit can include a large language model (LLM) that processes audio conversations to identify keywords and phrases, and various algorithms that detect and classify objects, humans, physical actions, and task objectives. The AI toolkit is designed to run continuously in the background, similarly to the subconscious filtering that the brain’s RAS performs.

The second stage is a “trigger check” phase, which is a periodic check that the system performs to assess if anything important is happening, such as whether a human is present or not. If a human has stepped into the environment, the system’s third phase will kick in. This phase is the heart of the team’s system, which acts to determine the features in the environment that are most likely relevant to assist the human.

To establish relevance, the researchers developed an algorithm that takes in real-time predictions made by the AI toolkit. For instance, the toolkit’s LLM may pick up the keyword “coffee,” and an action-classifying algorithm may label a person reaching for a cup as having the objective of “making coffee.” The team’s Relevance method would factor in this information to first determine the “class” of objects that have the highest probability of being relevant to the objective of “making coffee.” This might automatically filter out classes such as “fruits” and “snacks,” in favor of “cups” and “creamers.” The algorithm would then further filter within the relevant classes to determine the most relevant “elements.” For instance, based on visual cues of the environment, the system may label a cup closest to a person as more relevant — and helpful — than a cup that is farther away.

In the fourth and final phase, the robot would then take the identified relevant objects and plan a path to physically access and offer the objects to the human.

Helper mode

The researchers tested the new system in experiments that simulate a conference breakfast buffet. They chose this scenario based on the publicly available Breakfast Actions Dataset, which comprises videos and images of typical activities that people perform during breakfast time, such as preparing coffee, cooking pancakes, making cereal, and frying eggs. Actions in each video and image are labeled, along with the overall objective (frying eggs, versus making coffee).

Using this dataset, the team tested various algorithms in their AI toolkit, such that, when receiving actions of a person in a new scene, the algorithms could accurately label and classify the human tasks and objectives, and the associated relevant objects.

In their experiments, they set up a robotic arm and gripper and instructed the system to assist humans as they approached a table filled with various drinks, snacks, and tableware. They found that when no humans were present, the robot’s AI toolkit operated continuously in the background, labeling and classifying objects on the table.

When, during a trigger check, the robot detected a human, it snapped to attention, turning on its Relevance phase and quickly identifying objects in the scene that were most likely to be relevant, based on the human’s objective, which was determined by the AI toolkit.

“Relevance can guide the robot to generate seamless, intelligent, safe, and efficient assistance in a highly dynamic environment,” says co-author Zhang.

Going forward, the team hopes to apply the system to scenarios that resemble workplace and warehouse environments, as well as to other tasks and objectives typically performed in household settings.

“I would want to test this system in my home to see, for instance, if I’m reading the paper, maybe it can bring me coffee. If I’m doing laundry, it can bring me a laundry pod. If I’m doing repair, it can bring me a screwdriver,” Zhang says. “Our vision is to enable human-robot interactions that can be much more natural and fluent.”

This research was made possible by the support and partnership of King Abdulaziz City for Science and Technology (KACST) through the Center for Complex Engineering Systems at MIT and KACST.
',
 'Jennifer Chu',
 'MIT News',
 'Easy', 0, NULL, '2026-03-18 10:14:00', '2026-03-18 10:14:00'),
 
(15, 'Computer Science',
 'Explained: Generative AI''s Environmental Impact',
 'explained-generative-ais-environmental-impact',
 'In a two-part series, MIT News explores the environmental implications of generative AI. In this article, we look at why this technology is so resource-intensive. A second piece will investigate what experts are doing to reduce genAI’s carbon footprint and other impacts.

The excitement surrounding potential benefits of generative AI, from improving worker productivity to advancing scientific research, is hard to ignore. While the explosive growth of this new technology has enabled rapid deployment of powerful models in many industries, the environmental consequences of this generative AI “gold rush” remain difficult to pin down, let alone mitigate.

The computational power required to train generative AI models that often have billions of parameters, such as OpenAI’s GPT-4, can demand a staggering amount of electricity, which leads to increased carbon dioxide emissions and pressures on the electric grid.

Furthermore, deploying these models in real-world applications, enabling millions to use generative AI in their daily lives, and then fine-tuning the models to improve their performance draws large amounts of energy long after a model has been developed.

Beyond electricity demands, a great deal of water is needed to cool the hardware used for training, deploying, and fine-tuning generative AI models, which can strain municipal water supplies and disrupt local ecosystems. The increasing number of generative AI applications has also spurred demand for high-performance computing hardware, adding indirect environmental impacts from its manufacture and transport.

“When we think about the environmental impact of generative AI, it is not just the electricity you consume when you plug the computer in. There are much broader consequences that go out to a system level and persist based on actions that we take,” says Elsa A. Olivetti, professor in the Department of Materials Science and Engineering and the lead of the Decarbonization Mission of MIT’s new Climate Project.

Olivetti is senior author of a 2024 paper, “The Climate and Sustainability Implications of Generative AI,” co-authored by MIT colleagues in response to an Institute-wide call for papers that explore the transformative potential of generative AI, in both positive and negative directions for society.

Demanding data centers

The electricity demands of data centers are one major factor contributing to the environmental impacts of generative AI, since data centers are used to train and run the deep learning models behind popular tools like ChatGPT and DALL-E.

A data center is a temperature-controlled building that houses computing infrastructure, such as servers, data storage drives, and network equipment. For instance, Amazon has more than 100 data centers worldwide, each of which has about 50,000 servers that the company uses to support cloud computing services.

While data centers have been around since the 1940s (the first was built at the University of Pennsylvania in 1945 to support the first general-purpose digital computer, the ENIAC), the rise of generative AI has dramatically increased the pace of data center construction.

“What is different about generative AI is the power density it requires. Fundamentally, it is just computing, but a generative AI training cluster might consume seven or eight times more energy than a typical computing workload,” says Noman Bashir, lead author of the impact paper, who is a Computing and Climate Impact Fellow at MIT Climate and Sustainability Consortium (MCSC) and a postdoc in the Computer Science and Artificial Intelligence Laboratory (CSAIL).

Scientists have estimated that the power requirements of data centers in North America increased from 2,688 megawatts at the end of 2022 to 5,341 megawatts at the end of 2023, partly driven by the demands of generative AI. Globally, the electricity consumption of data centers rose to 460 terawatt-hours in 2022. This would have made data centers the 11th largest electricity consumer in the world, between the nations of Saudi Arabia (371 terawatt-hours) and France (463 terawatt-hours), according to the Organization for Economic Co-operation and Development.

By 2026, the electricity consumption of data centers is expected to approach 1,050 terawatt-hours (which would bump data centers up to fifth place on the global list, between Japan and Russia).

While not all data center computation involves generative AI, the technology has been a major driver of increasing energy demands.

“The demand for new data centers cannot be met in a sustainable way. The pace at which companies are building new data centers means the bulk of the electricity to power them must come from fossil fuel-based power plants,” says Bashir.

The power needed to train and deploy a model like OpenAI’s GPT-3 is difficult to ascertain. In a 2021 research paper, scientists from Google and the University of California at Berkeley estimated the training process alone consumed 1,287 megawatt hours of electricity (enough to power about 120 average U.S. homes for a year), generating about 552 tons of carbon dioxide.

While all machine-learning models must be trained, one issue unique to generative AI is the rapid fluctuations in energy use that occur over different phases of the training process, Bashir explains.

Power grid operators must have a way to absorb those fluctuations to protect the grid, and they usually employ diesel-based generators for that task.

Increasing impacts from inference

Once a generative AI model is trained, the energy demands don’t disappear.

Each time a model is used, perhaps by an individual asking ChatGPT to summarize an email, the computing hardware that performs those operations consumes energy. Researchers have estimated that a ChatGPT query consumes about five times more electricity than a simple web search.

“But an everyday user doesn’t think too much about that,” says Bashir. “The ease-of-use of generative AI interfaces and the lack of information about the environmental impacts of my actions means that, as a user, I don’t have much incentive to cut back on my use of generative AI.”

With traditional AI, the energy usage is split fairly evenly between data processing, model training, and inference, which is the process of using a trained model to make predictions on new data. However, Bashir expects the electricity demands of generative AI inference to eventually dominate since these models are becoming ubiquitous in so many applications, and the electricity needed for inference will increase as future versions of the models become larger and more complex.

Plus, generative AI models have an especially short shelf-life, driven by rising demand for new AI applications. Companies release new models every few weeks, so the energy used to train prior versions goes to waste, Bashir adds. New models often consume more energy for training, since they usually have more parameters than their predecessors.

While electricity demands of data centers may be getting the most attention in research literature, the amount of water consumed by these facilities has environmental impacts, as well.

Chilled water is used to cool a data center by absorbing heat from computing equipment. It has been estimated that, for each kilowatt hour of energy a data center consumes, it would need two liters of water for cooling, says Bashir.

“Just because this is called ‘cloud computing’ doesn’t mean the hardware lives in the cloud. Data centers are present in our physical world, and because of their water usage they have direct and indirect implications for biodiversity,” he says.

The computing hardware inside data centers brings its own, less direct environmental impacts.

While it is difficult to estimate how much power is needed to manufacture a GPU, a type of powerful processor that can handle intensive generative AI workloads, it would be more than what is needed to produce a simpler CPU because the fabrication process is more complex. A GPU’s carbon footprint is compounded by the emissions related to material and product transport.

There are also environmental implications of obtaining the raw materials used to fabricate GPUs, which can involve dirty mining procedures and the use of toxic chemicals for processing.

Market research firm TechInsights estimates that the three major producers (NVIDIA, AMD, and Intel) shipped 3.85 million GPUs to data centers in 2023, up from about 2.67 million in 2022. That number is expected to have increased by an even greater percentage in 2024.

The industry is on an unsustainable path, but there are ways to encourage responsible development of generative AI that supports environmental objectives, Bashir says.

He, Olivetti, and their MIT colleagues argue that this will require a comprehensive consideration of all the environmental and societal costs of generative AI, as well as a detailed assessment of the value in its perceived benefits.

“We need a more contextual way of systematically and comprehensively understanding the implications of new developments in this space. Due to the speed at which there have been improvements, we haven’t had a chance to catch up with our abilities to measure and understand the tradeoffs,” Olivetti says.
',
 'Adam Zewe',
 'MIT News',
 'Intermediate', 0, NULL, '2026-03-18 10:15:00', '2026-03-18 10:15:00'),
 
(16, 'Computer Science',
 'Expanding Robot Perception',
 'expanding-robot-perception',
 'Robots have come a long way since the Roomba. Today, drones are starting to deliver door to door, self-driving cars are navigating some roads, robo-dogs are aiding first responders, and still more bots are doing backflips and helping out on the factory floor. Still, Luca Carlone thinks the best is yet to come.

Carlone, who recently received tenure as an associate professor in MIT’s Department of Aeronautics and Astronautics (AeroAstro), directs the SPARK Lab, where he and his students are bridging a key gap between humans and robots: perception. The group does theoretical and experimental research, all toward expanding a robot’s awareness of its environment in ways that approach human perception. And perception, as Carlone often says, is more than detection.

While robots have grown by leaps and bounds in terms of their ability to detect and identify objects in their surroundings, they still have a lot to learn when it comes to making higher-level sense of their environment. As humans, we perceive objects with an intuitive sense of not just of their shapes and labels but also their physics — how they might be manipulated and moved — and how they relate to each other, their larger environment, and ourselves.

That kind of human-level perception is what Carlone and his group are hoping to impart to robots, in ways that enable them to safely and seamlessly interact with people in their homes, workplaces, and other unstructured environments.

Since joining the MIT faculty in 2017, Carlone has led his team in developing and applying perception and scene-understanding algorithms for various applications, including autonomous underground search-and-rescue vehicles, drones that can pick up and manipulate objects on the fly, and self-driving cars. They might also be useful for domestic robots that follow natural language commands and potentially even anticipate human’s needs based on higher-level contextual clues.

“Perception is a big bottleneck toward getting robots to help us in the real world,” Carlone says. “If we can add elements of cognition and reasoning to robot perception, I believe they can do a lot of good.”

Expanding horizons

Carlone was born and raised near Salerno, Italy, close to the scenic Amalfi coast, where he was the youngest of three boys. His mother is a retired elementary school teacher who taught math, and his father is a retired history professor and publisher, who has always taken an analytical approach to his historical research. The brothers may have unconsciously adopted their parents’ mindsets, as all three went on to be engineers — the older two pursued electronics and mechanical engineering, while Carlone landed on robotics, or mechatronics, as it was known at the time.

He didn’t come around to the field, however, until late in his undergraduate studies. Carlone attended the Polytechnic University of Turin, where he focused initially on theoretical work, specifically on control theory — a field that applies mathematics to develop algorithms that automatically control the behavior of physical systems, such as power grids, planes, cars, and robots. Then, in his senior year, Carlone signed up for a course on robotics that explored advances in manipulation and how robots can be programmed to move and function.

“It was love at first sight. Using algorithms and math to develop the brain of a robot and make it move and interact with the environment is one of the most fulfilling experiences,” Carlone says. “I immediately decided this is what I want to do in life.”

He went on to a dual-degree program at the Polytechnic University of Turin and the Polytechnic University of Milan, where he received master’s degrees in mechatronics and automation engineering, respectively. As part of this program, called the Alta Scuola Politecnica, Carlone also took courses in management, in which he and students from various academic backgrounds had to team up to conceptualize, build, and draw up a marketing pitch for a new product design. Carlone’s team developed a touch-free table lamp designed to follow a user’s hand-driven commands. The project pushed him to think about engineering from different perspectives.

“It was like having to speak different languages,” he says. “It was an early exposure to the need to look beyond the engineering bubble and think about how to create technical work that can impact the real world.”

The next generation

Carlone stayed in Turin to complete his PhD in mechatronics. During that time, he was given freedom to choose a thesis topic, which he went about, as he recalls, “a bit naively.”

“I was exploring a topic that the community considered to be well-understood, and for which many researchers believed there was nothing more to say.” Carlone says. “I underestimated how established the topic was, and thought I could still contribute something new to it, and I was lucky enough to just do that.”

The topic in question was “simultaneous localization and mapping,” or SLAM — the problem of generating and updating a map of a robot’s environment while simultaneously keeping track of where the robot is within that environment. Carlone came up with a way to reframe the problem, such that algorithms could generate more precise maps without having to start with an initial guess, as most SLAM methods did at the time. His work helped to crack open a field where most roboticists thought one could not do better than the existing algorithms.

“SLAM is about figuring out the geometry of things and how a robot moves among those things,” Carlone says. “Now I’m part of a community asking, what is the next generation of SLAM?”

In search of an answer, he accepted a postdoc position at Georgia Tech, where he dove into coding and computer vision — a field that, in retrospect, may have been inspired by a brush with blindness: As he was finishing up his PhD in Italy, he suffered a medical complication that severely affected his vision.

“For one year, I could have easily lost an eye,” Carlone says. “That was something that got me thinking about the importance of vision, and artificial vision.”

He was able to receive good medical care, and the condition resolved entirely, such that he could continue his work. At Georgia Tech, his advisor, Frank Dellaert, showed him ways to code in computer vision and formulate elegant mathematical representations of complex, three-dimensional problems. His advisor was also one of the first to develop an open-source SLAM library, called GTSAM, which Carlone quickly recognized to be an invaluable resource. More broadly, he saw that making software available to all unlocked a huge potential for progress in robotics as a whole.

“Historically, progress in SLAM has been very slow, because people kept their codes proprietary, and each group had to essentially start from scratch,” Carlone says. “Then open-source pipelines started popping up, and that was a game changer, which has largely driven the progress we have seen over the last 10 years.”

Spatial AI

Following Georgia Tech, Carlone came to MIT in 2015 as a postdoc in the Laboratory for Information and Decision Systems (LIDS). During that time, he collaborated with Sertac Karaman, professor of aeronautics and astronautics, in developing software to help palm-sized drones navigate their surroundings using very little on-board power. A year later, he was promoted to research scientist, and then in 2017, Carlone accepted a faculty position in AeroAstro.

“One thing I fell in love with at MIT was that all decisions are driven by questions like: What are our values? What is our mission? It’s never about low-level gains. The motivation is really about how to improve society,” Carlone says. “As a mindset, that has been very refreshing.”

Today, Carlone’s group is developing ways to represent a robot’s surroundings, beyond characterizing their geometric shape and semantics. He is utilizing deep learning and large language models to develop algorithms that enable robots to perceive their environment through a higher-level lens, so to speak. Over the last six years, his lab has released more than 60 open-source repositories, which are used by thousands of researchers and practitioners worldwide. The bulk of his work fits into a larger, emerging field known as “spatial AI.”

“Spatial AI is like SLAM on steroids,” Carlone says. “In a nutshell, it has to do with enabling robots to think and understand the world as humans do, in ways that can be useful.”

It’s a huge undertaking that could have wide-ranging impacts, in terms of enabling more intuitive, interactive robots to help out at home, in the workplace, on the roads, and in remote and potentially dangerous areas. Carlone says there will be plenty of work ahead, in order to come close to how humans perceive the world.

“I have 2-year-old twin daughters, and I see them manipulating objects, carrying 10 different toys at a time, navigating across cluttered rooms with ease, and quickly adapting to new environments. Robot perception cannot yet match what a toddler can do,” Carlone says. “But we have new tools in the arsenal. And the future is bright.”
',
 'Jennifer Chu',
 'MIT News',
 'Intermediate', 0, NULL, '2026-03-18 10:16:00', '2026-03-18 10:16:00'),
 
(17, 'Computer Science',
 'Quantum Algorithms Struggle Against Old Foe: Clever Computers',
 'quantum-algorithms-struggle-against-old-foe-clever-computers',
 'A popular misconception is that the potential — and the limits — of quantum computing must come from hardware. In the digital age, we’ve gotten used to marking advances in clock speed and memory. Likewise, the 50-qubit quantum machines now coming online from the likes of Intel and IBM have inspired predictions that we are nearing “quantum supremacy” — a nebulous frontier where quantum computers begin to do things beyond the ability of classical machines.

But quantum supremacy is not a single, sweeping victory to be sought — a broad Rubicon to be crossed — but rather a drawn-out series of small duels. It will be established problem by problem, quantum algorithm versus classical algorithm. “With quantum computers, progress is not just about speed,” said Michael Bremner(opens a new tab), a quantum theorist at the University of Technology Sydney. “It’s much more about the intricacy of the algorithms at play.”

Paradoxically, reports of powerful quantum computations are motivating improvements to classical ones, making it harder for quantum machines to gain an advantage. “Most of the time when people talk about quantum computing, classical computing is dismissed, like something that is past its prime,” said Cristian Calude(opens a new tab), a mathematician and computer scientist at the University of Auckland in New Zealand. “But that is not the case. This is an ongoing competition.”

And the goalposts are shifting. “When it comes to saying where the supremacy threshold is, it depends on how good the best classical algorithms are,” said John Preskill(opens a new tab), a theoretical physicist at the California Institute of Technology. “As they get better, we have to move that boundary.”

‘It Doesn’t Look So Easy’
Before the dream of a quantum computer took shape in the 1980s, most computer scientists took for granted that classical computing was all there was. The field’s pioneers had convincingly argued that classical computers — epitomized by the mathematical abstraction known as a Turing machine — should be able to compute everything that is computable in the physical universe, from basic arithmetic to stock trades to black hole collisions.

Classical machines couldn’t necessarily do all these computations efficiently, though. Let’s say you wanted to understand something like the chemical behavior of a molecule. This behavior depends on the behavior of the electrons in the molecule, which exist in a superposition of many classical states. Making things messier, the quantum state of each electron depends on the states of all the others — due to the quantum-mechanical phenomenon known as entanglement. Classically calculating these entangled states in even very simple molecules can become a nightmare of exponentially increasing complexity.

A quantum computer, by contrast, can deal with the intertwined fates of the electrons under study by superposing and entangling its own quantum bits. This enables the computer to process extraordinary amounts of information. Each single qubit you add doubles the states the system can simultaneously store: Two qubits can store four states, three qubits can store eight states, and so on. Thus, you might need just 50 entangled qubits to model quantum states that would require exponentially many classical bits — 1.125 quadrillion to be exact — to encode.

A quantum machine could therefore make the classically intractable problem of simulating large quantum-mechanical systems tractable, or so it appeared. “Nature isn’t classical, dammit, and if you want to make a simulation of nature, you’d better make it quantum mechanical,” the physicist Richard Feynman famously quipped in 1981. “And by golly it’s a wonderful problem, because it doesn’t look so easy.”

It wasn’t, of course.

Even before anyone began tinkering with quantum hardware, theorists struggled to come up with suitable software. Early on, Feynman and David Deutsch(opens a new tab), a physicist at the University of Oxford, learned that they could control quantum information with mathematical operations borrowed from linear algebra, which they called gates. As analogues to classical logic gates, quantum gates manipulate qubits in all sorts of ways — guiding them into a succession of superpositions and entanglements and then measuring their output. By mixing and matching gates to form circuits, the theorists could easily assemble quantum algorithms.

Conceiving algorithms that promised clear computational benefits proved more difficult. By the early 2000s, mathematicians had come up with only a few good candidates. Most famously, in 1994, a young staffer at Bell Laboratories named Peter Shor(opens a new tab) proposed a quantum algorithm(opens a new tab) that factors integers exponentially faster than any known classical algorithm — an efficiency that could allow it to crack many popular encryption schemes. Two years later, Shor’s Bell Labs colleague Lov Grover devised an algorithm(opens a new tab) that speeds up the classically tedious process of searching through unsorted databases. “There were a variety of examples that indicated quantum computing power should be greater than classical,” said Richard Jozsa(opens a new tab), a quantum information scientist at the University of Cambridge.

But Jozsa, along with other researchers, would also discover a variety of examples that indicated just the opposite. “It turns out that many beautiful quantum processes look like they should be complicated” and therefore hard to simulate on a classical computer, Jozsa said. “But with clever, subtle mathematical techniques, you can figure out what they will do.” He and his colleagues found that they could use these techniques to efficiently simulate — or “de-quantize,” as Calude would say — a surprising number of quantum circuits. For instance, circuits that omit entanglement fall into this trap, as do those that entangle only a limited number of qubits or use only certain kinds of entangling gates.

What, then, guarantees that an algorithm like Shor’s is uniquely powerful? “That’s very much an open question,” Jozsa said. “We never really succeeded in understanding why some [algorithms] are easy to simulate classically and others are not. Clearly entanglement is important, but it’s not the end of the story.” Experts began to wonder whether many of the quantum algorithms that they believed were superior might turn out to be only ordinary.

Sampling Struggle
Until recently, the pursuit of quantum power was largely an abstract one. “We weren’t really concerned with implementing our algorithms because nobody believed that in the reasonable future we’d have a quantum computer to do it,” Jozsa said. Running Shor’s algorithm for integers large enough to unlock a standard 128-bit encryption key, for instance, would require thousands of qubits — plus probably many thousands more to correct for errors. Experimentalists, meanwhile, were fumbling while trying to control more than a handful.

But by 2011, things were starting to look up. That fall, at a conference in Brussels, Preskill speculated(opens a new tab) that “the day when well-controlled quantum systems can perform tasks surpassing what can be done in the classical world” might not be far off. Recent laboratory results, he said, could soon lead to quantum machines on the order of 100 qubits. Getting them to pull off some “super-classical” feat maybe wasn’t out of the question. (Although D-Wave Systems’ commercial quantum processors could by then wrangle 128 qubits and now boast more than 2,000, they tackle only specific optimization problems; many experts doubt they can outperform classical computers.)

“I was just trying to emphasize we were getting close — that we might finally reach a real milestone in human civilization where quantum technology becomes the most powerful information technology that we have,” Preskill said. He called this milestone “quantum supremacy.” The name — and the optimism — stuck. “It took off to an extent I didn’t suspect.”

The buzz about quantum supremacy reflected a growing excitement in the field — over experimental progress, yes, but perhaps more so over a series of theoretical breakthroughs that began with a 2004 paper(opens a new tab) by the IBM physicists Barbara Terhal(opens a new tab) and David DiVincenzo(opens a new tab). In their effort to understand quantum assets, the pair had turned their attention to rudimentary quantum puzzles known as sampling problems. In time, this class of problems would become experimentalists’ greatest hope for demonstrating an unambiguous speedup on early quantum machines.

Sampling problems exploit the elusive nature of quantum information. Say you apply a sequence of gates to 100 qubits. This circuit may whip the qubits into a mathematical monstrosity equivalent to something on the order of 2100 classical bits. But once you measure the system, its complexity collapses to a string of only 100 bits. The system will spit out a particular string — or sample — with some probability determined by your circuit.

In a sampling problem, the goal is to produce a series of samples that look as though they came from this circuit. It’s like repeatedly tossing a coin to show that it will (on average) come up 50 percent heads and 50 percent tails. Except here, the outcome of each “toss” isn’t a single value — heads or tails — it’s a string of many values, each of which may be influenced by some (or even all) of the other values.

For a well-oiled quantum computer, this exercise is a no-brainer. It’s what it does naturally. Classical computers, on the other hand, seem to have a tougher time. In the worst circumstances, they must do the unwieldy work of computing probabilities for all possible output strings — all 2100 of them — and then randomly select samples from that distribution. “People always conjectured this was the case,” particularly for very complex quantum circuits, said Ashley Montanaro(opens a new tab), an expert in quantum algorithms at the University of Bristol.

Terhal and DiVincenzo showed that even some simple quantum circuits should still be hard to sample by classical means. Hence, a bar was set. If experimentalists could get a quantum system to spit out these samples, they would have good reason to believe that they’d done something classically unmatchable.

Theorists soon expanded this line of thought to include other sorts of sampling problems. One of the most promising proposals came from Scott Aaronson(opens a new tab), a computer scientist then at the Massachusetts Institute of Technology, and his doctoral student Alex Arkhipov. In work posted on the scientific preprint site arxiv.org in 2010(opens a new tab), they described a quantum machine that sends photons through an optical circuit, which shifts and splits the light in quantum-mechanical ways, thereby generating output patterns with specific probabilities. Reproducing these patterns became known as boson sampling. Aaronson and Arkhipov reasoned that boson sampling would start to strain classical resources at around 30 photons — a plausible experimental target.

Similarly enticing were computations called instantaneous quantum polynomial, or IQP, circuits. An IQP circuit has gates that all commute, meaning they can act in any order without changing the outcome — in the same way 2 + 5 = 5 + 2. This quality makes IQP circuits mathematically pleasing. “We started studying them because they were easier to analyze,” Bremner said. But he discovered that they have other merits. In work that began in 2010(opens a new tab) and culiminated in a 2016 paper(opens a new tab) with Montanaro and Dan Shepherd, now at the National Cyber Security Center in the U.K., Bremner explained why IQP circuits can be extremely powerful: Even for physically realistic systems of hundreds — or perhaps even dozens — of qubits, sampling would quickly become a classically thorny problem.

By 2016, boson samplers had yet to extend beyond 6 photons(opens a new tab). Teams at Google and IBM, however, were verging on chips nearing 50 qubits; that August, Google quietly posted a draft paper(opens a new tab) laying out a road map for demonstrating quantum supremacy on these “near-term” devices.

Google’s team had considered sampling from an IQP circuit. But a closer look(opens a new tab) by Bremner and his collaborators suggested that the circuit would likely need some error correction — which would require extra gates and at least a couple hundred extra qubits — in order to unequivocally hamstring the best classical algorithms. So instead, the team used arguments akin to Aaronson’s and Bremner’s to show that circuits made of non-commuting gates, although likely harder to build and analyze than IQP circuits, would also be harder for a classical device to simulate. To make the classical computation even more challenging, the team proposed sampling from a circuit chosen at random. That way, classical competitors would be unable to exploit any familiar features of the circuit’s structure to better guess its behavior.

But there was nothing to stop the classical algorithms from getting more resourceful. In fact, in October 2017, a team at IBM showed how(opens a new tab), with a bit of classical ingenuity, a supercomputer can simulate sampling from random circuits on as many as 56 qubits — provided the circuits don’t involve too much depth (layers of gates). Similarly, a more able algorithm(opens a new tab) has recently nudged the classical limits of boson sampling, to around 50 photons.

These upgrades, however, are still dreadfully inefficient. IBM’s simulation, for instance, took two days to do what a quantum computer is expected to do in less than one-tenth of a millisecond. Add a couple more qubits — or a little more depth — and quantum contenders could slip freely into supremacy territory. “Generally speaking, when it comes to emulating highly entangled systems, there has not been a [classical] breakthrough that has really changed the game,” Preskill said. “We’re just nibbling at the boundary rather than exploding it.”

That’s not to say there will be a clear victory. “Where the frontier is is a thing people will continue to debate,” Bremner said. Imagine this scenario: Researchers sample from a 50-qubit circuit of some depth — or maybe a slightly larger one of less depth — and claim supremacy. But the circuit is pretty noisy — the qubits are misbehaving, or the gates don’t work that well. So then some crackerjack classical theorists swoop in and simulate the quantum circuit, no sweat, because “with noise, things you think are hard become not so hard from a classical point of view,” Bremner explained. “Probably that will happen.”

What’s more certain is that the first “supreme” quantum machines, if and when they arrive, aren’t going to be cracking encryption codes or simulating novel pharmaceutical molecules. “That’s the funny thing about supremacy,” Montanaro said. “The first wave of problems we solve are ones for which we don’t really care about the answers.”

Yet these early wins, however small, will assure scientists that they are on the right track — that a new regime of computation really is possible. Then it’s anyone’s guess what the next wave of problems will be.
',
 'Ariel Bleicher',
 'Quanta Magazine',
 'Advanced', 0, NULL, '2026-03-18 10:17:00', '2026-03-18 10:17:00'),
 
(18, 'Computer Science',
 'Computer Scientists Establish the Best Way to Traverse a Graph',
 'computer-scientists-establish-best-way-traverse-graph',
 'If you’ve been making the same commute for a long time, you’ve probably settled on what seems like the best route. But “best” is a slippery concept. Perhaps one day there’s an accident or road closure, and your fastest route becomes the slowest.

Scenarios like this are also a challenge for researchers who develop algorithms, the step-by-step procedures that computers use to solve problems. Many different algorithms can solve any given problem, and the question of which is best can be frustratingly ambiguous.

For example, imagine an algorithm that’s designed to find the fastest route between two points. There are lots of possible ways to design such an algorithm so that it doesn’t fail. A successful algorithm will always return the fastest route, whether you use it in London or Los Angeles, and whether it’s rush hour or the middle of the night.

But those algorithms aren’t all the same. The time each one takes to find the right answer will vary depending on where and when it’s used, and cases that are hard for one algorithm may be easy for another. Ideally, you’d want an algorithm that always runs faster than the others.

For most problems, it’s simply not possible to find such a unicorn. But a new proof(opens a new tab) shows that for the quintessential path-finding problem, one algorithm is close to ideal: Assuming worst-case traffic patterns, it’s the best approach on every possible street grid. What’s more, the algorithm is nearly 70 years old and a staple of the undergraduate computer science curriculum. The new work will be presented with a best-paper award at the 2024 Symposium on Foundations of Computer Science next week.

“It’s amazing,” said Tim Roughgarden(opens a new tab), a computer scientist at Columbia University. “I can’t imagine a more compelling research paper about a problem we teach students in undergrad algorithms.”

Heaps and Bounds
The story of this iconic path-finding algorithm began with a detour. In 1956, the 26-year-old Dutch computer scientist Edsger Dijkstra wanted to write a program that would show off the capabilities of a brand-new computer called the ARMAC. While shopping with his fiancée in Amsterdam, he stopped in at a café for a break. That’s when he hit on the idea for the algorithm(opens a new tab) that now bears his name. He didn’t have writing materials on hand, so over the course of 20 minutes he worked out the details in his head.

In an interview(opens a new tab) toward the end of his life, Dijkstra credited his algorithm’s enduring appeal in part to its unusual origin story. “Without pencil and paper you are almost forced to avoid all avoidable complexities,” he said.

Dijkstra’s algorithm doesn’t just tell you the fastest route to one destination. Instead, it gives you an ordered list of travel times from your current location to every other point that you might want to visit — a solution to what researchers call the single-source shortest-paths problem. The algorithm works in an abstracted road map called a graph: a network of interconnected points (called vertices) in which the links between vertices are labeled with numbers (called weights). These weights might represent the time required to traverse each road in a network, and they can change depending on traffic patterns. The larger a weight, the longer it takes to traverse that path.

To get a sense of Dijkstra’s algorithm, imagine yourself wandering through a graph, writing down the travel time from your starting point to each new vertex on a piece of scratch paper. Whenever you have a choice about which direction to explore next, head toward the closest vertex you haven’t visited yet. If you discover a faster route to any vertex, jot down the new time and cross out the old one. When you’re sure that you’ve found the fastest path, move the travel time from your notes to a separate, more presentable list.

“It’s a great algorithm,” said Erik Demaine(opens a new tab), a computer scientist at the Massachusetts Institute of Technology. “It’s very fast, simple and easy to implement.”

To put this procedure into practice, you’d need to decide on a system for organizing your notes — a data structure, in the lingo of computer science. That may sound like a minor technical detail, but time spent searching through your notes whenever you need to edit or remove an entry can have a big effect on the overall runtime of the algorithm.

Dijkstra’s paper used a simple data structure that left room for improvement. In the following decades, researchers developed better ones, affectionately dubbed “heaps,” in which certain items are easier to find than others. They take advantage of the fact that Dijkstra’s algorithm only ever needs to remove the entry for the closest remaining vertex. “A heap is basically a data structure that allows you to do this very quickly,” said Václav Rozhoň(opens a new tab), a researcher at the Institute for Computer Science, Artificial Intelligence and Technology (INSAIT) in Sofia, Bulgaria.

In 1984, two computer scientists developed a clever heap design(opens a new tab) that enabled Dijkstra’s algorithm to reach a theoretical limit, or “lower bound,” on the time required to solve the single-source shortest-paths problem. In one specific sense, this version of Dijkstra’s algorithm is the best possible. That was the last word on the standard version of the problem for nearly 40 years. Things only changed when a few researchers took a closer look at what it means to be “best.”

Best Behavior
Researchers typically compare algorithms by studying how they fare in worst-case scenarios. Imagine the world’s most confusing street grid, then add some especially perplexing traffic patterns. If you insist on finding the fastest routes in these extreme circumstances, the 1984 version of Dijkstra’s algorithm is provably unbeatable.

But hopefully, your city doesn’t have the world’s worst street grid. And so you may ask: Is there an algorithm that’s unbeatable on every road network? The first step to answering this question is to make the conservative assumption that each network has worst-case traffic patterns. Then you want your algorithm to find the fastest paths through any possible graph layout, assuming the worst possible weights. Researchers call this condition “universal optimality.” If you had a universally optimal algorithm for the simpler problem of just getting from one point on a graph to another, it could help you beat rush hour traffic in every city in the world.

“This sounds too good to be true,” said Bernhard Haeupler(opens a new tab), a computer scientist affiliated with INSAIT and the Swiss Federal Institute of Technology Zurich (ETH Zurich).

Haeupler became fascinated with the promise of universal optimality while writing a grant proposal in the mid-2010s. Many researchers find that part of the job tedious, but Haeupler saw it as an opportunity. “It allows you to throw your skepticism out and just dream big,” he said.

Those dreams came to fruition in 2021, when Haeupler and two graduate students proved(opens a new tab) that it was possible to build universally optimal algorithms for several important graph problems. He didn’t think to ask whether the same condition was achievable for the classic single-source shortest-paths problem. That would have to wait until a different graduate student dared to dream big.

The Shortest Path to Victory
In early 2023, Rozhoň was at the tail end of his graduate program at ETH Zurich. He had just finished a paper(opens a new tab) about going beyond worst-case analysis in a different context, and he was brainstorming new ideas to pursue with his co-author Jakub Tětek(opens a new tab), then a graduate student at the University of Copenhagen. Rozhoň suggested they try to devise a universally optimal algorithm for the single-source shortest-paths problem.

“I said, ‘No, but that’s not possible; that just cannot be done,’” Tětek recalled. But Rozhoň convinced him to give it a try. In the spring, the team grew to three with the addition of Richard Hladík(opens a new tab), a graduate student at ETH Zurich whom Rozhoň and Tětek had met when all three were high schoolers in the Czech Republic.

The trio tinkered with many different aspects of Dijkstra’s algorithm and the heap that it used, and they managed to kludge together a universally optimal variant. But the resulting algorithm was complicated, and they couldn’t pinpoint what conditions were actually necessary for universal optimality. In a field that thrives on comprehensive and rigorous proofs, this wasn’t enough.

The three students would turn from mathematical networks to social ones. Rozhoň had begun discussing the problem with Haeupler while both were visiting colleagues in New York. From there, Haeupler flew to Panama for a holiday, but he wasn’t quite ready to set the problem aside.

“It really was vacation,” he said. “But then also, thinking doesn’t stop.”

During a Zoom call a few days into Haeupler’s trip, the team of four settled on a new approach. They decided to focus mainly on the choice of data structure. Soon, they began to suspect that that would be enough — they could just leave the rest of Dijkstra’s algorithm intact. Within a month, they’d proved it.

The key ingredient turned out to be a special property of some data structures that lets them quickly access recently added items. Heaps with this property were first constructed(opens a new tab) over 20 years ago, but in all the years that followed, nobody made full use of it. The four researchers proved that they only needed to construct a data structure with this new property and all the other features of the 1984 heap. All they needed to do now was design it.

The last person to join the team was Robert Tarjan(opens a new tab), a computer scientist at Princeton University who was one of the inventors of that special 1984 heap. Tarjan had won the Turing Award, considered the highest honor in the field, and had also been a mentor to Haeupler in the late 2000s. When Tarjan visited Zurich in May, Haeupler invited him over for fondue — his specialty — and mentioned the new shortest-paths project. Tarjan was immediately in.

The five researchers set to work developing a heap data structure with all the properties they needed. They started with an unwieldy design and improved it bit by bit until they were finally satisfied. “Every time we looked at it, we were able to simplify a little bit,” Rozhoň said. “I was actually surprised how simple it was in the end.”

Some variants of Dijkstra’s algorithm have seen real-world use in software like Google Maps. The new result probably won’t have such practical applications, for which there are many considerations beyond theoretical optimality guarantees. But it may change how researchers study optimality, prompting them to look beyond the usual worst-case analysis. Often, algorithms only achieve stronger guarantees at the cost of added complexity. The new result suggests that simple algorithms with these stronger guarantees might be more widespread than researchers previously thought — the team has already identified two(opens a new tab) other(opens a new tab) examples.

“The general concept is very compelling,” Tarjan said. “It remains to be seen how far one can take this.”
',
 'Ben Brubaker',
 'Quanta Magazine',
 'Advanced', 0, NULL, '2026-03-18 10:18:00', '2026-03-18 10:18:00');
 
-- ----------------------------------------
-- Subject 4: Mechanical Engineering (articles 19-24)
-- ----------------------------------------
INSERT INTO articles (article_id, subject, title, slug, content, author, source, level, read_count, deleted_at, created_at, updated_at) VALUES
(19, 'Mechanical Engineering',
 'Top 10 Materials for 3D Printing',
 'top-10-materials-for-3d-printing',
 'The excitement around the promise of 3D printing has opened the floodgates. New printers are being developed every day to print all sorts of materials from plastics, metals, composites, and concrete, to organic materials, paper, and food.

“It shows what a hot and exciting field it is,” says Jesse Darley, director of mechanical engineering for Design Concepts, a Madison, WI-based product design and innovation consultancy.

Darley’s favorites as a product developer begin with those currently in use and those undergoing testing and are followed up by materials of interest in other fields. Below are his top 10 favorite materials for 3D printing (beyond common and improved plastics), starting with current ones.

1. Sintered powdered metal
Used for “printing” injection molds and sacrificial fixtures that accelerate the design process for traditional manufacturing methods like injection molding, casting, and lay-up. “One of the cooler applications is for carbon fiber lay-up,” Darley says. “You print the lattice and lay out the carbon fiber [around the mold]. Then you put it in a bath, and the support material melts away.”

2. Metals, such as stainless, bronze, steel, gold, nickel steel, aluminum, and titanium
These are printed directly by binding metal dust and firing it to become a hard part. This process can replace casting and post-processing and turn material directly into a functional metal part that can be electropolished or machined to finish the items. Prototyping is the best example of this application, but it is also being used for medical devices, jewelry and other custom items, according to Darley.

3. Carbon fiber and other composites
A 3D machine first prints a plastic, like ABS, and then prints carbon fibers on top. “This is a more cost-effective and quicker way to print something as strong as or stronger than metal,” Darley says. “If it can be scaled up (right now the printers are fairly small), I see it replacing carbon fiber lay-up, a slow and time-consuming process.” This material is used in the bicycle and aeronautics industries.

4. Carbon nanotubes and graphene embedded in plastics
“The most awesome is graphene,” says Darley. “There is all this promise of graphene, its amazing strength, conductivity instead of connectivity, its size. You could make things like flexible touchscreens, solar panels, and building circuits made of extremely tough materials; that’s the only material I’ve seen where you create totally new technology, not just being able to do stuff faster or easier; this is something totally new.”

5. Nitinol
Nitinol is an alloy of nickel and titanium used in a lot of medical implants. Darley notes that this metal has two “amazing” properties: its superelasticity and the ability to change shapes. For catheter wires and stents, nitinol can bend further than anything else, he explains. “You can fold it in half and it will come back into its original shape.” Darley adds that because the metal is not an easy material to machine or create in a lot of different forms, 3D printing allows you to do things with medical products you couldn’t do before.

6. Water-absorbing plastic
Water-absorbing plastic is printed using a 4D printing process. The fourth dimension refers to a form an object can take after it is printed that is a different shape, possibly leading to self-assembly. For example, if something is being inserted into the body (or sea, or space), in a narrow tube but the end product needs to be a different shape, the object can transform itself after it is inserted.

7. Stem cells
Being able to print organs to replace an ear, a blood vessel, or a piece of the heart with such an implant is pretty amazing, Darley says.

8. Conductive carbomorph (carbon black plus plastic)
3D printing allows circuits and batteries to be built into plastic parts used to make devices. This could eliminate some assembly, leading to having additive manufacturing replacing most, if not all, other manufacturing processes. “There are very few plain plastics or mechanical parts these days; everything is electromechanical,” Darley says.

9. Paper
3D printed paper results in a full color model; one produced very inexpensively compared to traditional visual models that take hours of work for a realistic look and are used for client approval of a design prior to final engineering, Darley says. The printer glues together and trims layers of paper to create the shape and has the capability of adding color as well.

10. Concrete, food, yarn
In China, pieces of houses have been printed and then assembled. Food has been printed in all different shapes, and yarn has produced many soft materials. When you think about it, you can manipulate with the computer anything that can be squirted out, Darley adds.
',
 'Nancy S. Giges',
 'ASME',
 'Easy', 0, NULL, '2026-03-18 10:19:00', '2026-03-18 10:19:00'),
 
(20, 'Mechanical Engineering',
 'Nano: Engineering''s New Frontier',
 'nano-engineerings-new-frontier',
 'Nanoengineering is the manipulation of materials and processes at the nanoscale—about 1-100 nanometers. With so many advances over the last decade, nanotechnology has become the new frontier of engineering, creating endless possibilities for manufacturing, microfluidics, robotics, biomedicine, energy, heat transfer and storage, nanomaterials, and computational modeling. Nanoengineering is also one of the most interdisciplinary of the sciences, requiring knowledge of mechanical engineering, chemical engineering, electrical engineering, biology, physics, photonics, and materials science.

Popular research fields include nanoscale energy transport, conversion, and storage, nano and micro electromechanical systems, nanomaterials, and alternative energy systems, including solar photovoltaic devices. Chemically modified nanomaterials are having huge impacts on biochemical sensing and human health. Carbon-based nanomaterials continue to evolve and are known for high strength, conductivity, and light weight.

Nanosystems and Mechanical Engineers
Advances in nanoengineering expand the mechanical engineer’s toolbox. Naturally occurring materials have a certain range of material properties and functions that most mechanical engineers have utilized. In contrast, nanoengineered materials can be designed to provide enhanced properties such as biochemical sensitivity, mechanical strength, selective transport, thermal or electrical conductivity, and optical properties.

“Although the underlying science of nanotechnology is interesting and important, most mechanical engineers tend to focus on the parts of nanoengineering that best support their own particular design needs,” says Carol Livermore, associate professorof mechanical engineering at Northeastern University in Boston, MA. “For example, the strong, lightweight, high-conductivity nature of carbon nanomaterials makes them of high interest to MEs working on airborne and space applications.”

Products created with nanoengineering can often be incorporated into a mechanical engineer’s current work, with only a little additional training or education to use them effectively. An example is the integration of carbon nanotube yarns or sheets into airborne or space applications as shielding or electrical conductors.

Read About the Top 5 Nanoscale Manufacturing Processes

However, not all nanotech products can be used immediately; instead they require further testing to see if they work as intended in macroscale applications. The special properties of nanoengineered materials and structures are enabled by their tiny sizes. “When larger-scale systems take advantage of nanoengineering, their properties are determined by large ensembles of nanoscale structures and how they interact with each other and with the rest of the system,” Livermore says.

This can frustrate engineers at times—often the properties of larger-scale systems that use nanoengineered elements are less exceptional than the properties of the individual nanoscale elements themselves.

Disrupting the Disruptive
The specialized properties of nanomaterials continue to improve the performance of many products and processes. A good example is additive manufacturing and 3D printing—the most disruptive force in manufacturing is becoming even more so, thanks to new nanotechnology applications.

For example, Rutgers University researchers have developed a method for binding nanomaterials during additive manufacturing that could lead to faster and less-expensive manufacturing of flexible thin film devices, such as touch screens. The “intense pulsed light sintering” method uses high-energy light over an area nearly 7,000 times larger than a laser spot to fuse nanomaterials in seconds.

Engineers at the California Institute of Technology have discovered a way to 3D-print the smallest complex nanoscale metal structures ever created, with diameters of roughly 1/1000th the size of the tip of a sewing needle. The process involves mixing metal ions with organic ligands to create a structure that is then heated and shrunk at temperatures as high as 1,000 °C.

In September 2017 researchers from HRL Laboratories developed a new method for 3D printing high-strength metals and alloys using a technique called “nanoparticle functionalization.” The process involves placing specially selected nanoparticles over layers of high-strength metal alloy powders. During subsequent melting and solidification, the nanoparticles act as nucleation sites for the desired alloy microstructure, which retains its full alloy strength. Further, the researchers did not need to be nanoparticle experts themselves: to determine which nanoparticles had the properties they needed, they consulted a materials data firm that reduced the material possibilities from hundreds of thousands to only a few.

Greater Design Freedom
Continued research in nanotechnology and engineering will revolutionize production processes in many industries and established new technological platforms and infrastructure that will have major impacts on the U.S. and global economies.

For engineers who are inexperienced with nanoengineering and want to utilize it in their projects, Livermore recommends just “jumping in and doing it. Read independently and collaborate with people who are knowledgeable about the field to get up to speed,” she says.

Applying individual nanoelements to larger-scale systems often requires considerable research on how to control the architecture and interfaces of the nanoengineered structures in the larger system. This is one of the biggest challenges and often requires a multidisciplinary background.

“Because nanoscience and nanoengineering are built on a multidisciplinary science and engineering foundation, an ME background is excellent preparation both for creating nanoengineered systems and for turning the small-scale science that has already been developed into larger-scale engineering solutions,” Livermore says.
',
 'Mark Crawford',
 'ASME',
 'Easy', 0, NULL, '2026-03-18 10:20:00', '2026-03-18 10:20:00'),
 
(21, 'Mechanical Engineering',
 'Thermal Energy Storage Is No Longer Just Hot Air',
 'thermal-energy-storage-no-longer-just-hot-air',
 'Next year, the town of Colchester, England, will transplant four roughly 6-meter shipping containers onto the site of a new mixed-use development. The shipping containers, which house a Frankenstein-like assortment of machine parts—motors repurposed from Volvo truck engines, giant tanks of compressed air, huge silos of piping hot sand—are produced by a company called Cheesecake Energy.

Despite its name, Cheesecake Energy isn’t in the food business. The company is building these shipping-container systems, which work like giant batteries that store energy as heat and pressurized air, rather than a chemical reaction. (Cheesecake’s name is derived from a nerdy acronym for their technology.)

Cheesecake is part of a cohort of companies trying to meet a growing need for alternative forms of energy storage. As countries transition away from fossil fuels to green sources of energy like wind and solar, there will be natural lulls in energy production due to weather conditions. Energy consumption also tends to peak during early evening hours, which is inconveniently right when solar energy output decreases. Energy-storage technology is seen as a way to help even out the imbalance in supply and demand by storing excess energy during periods of high production and using it when needed.

Recent years have seen the construction of large lithium-ion battery farms that do just that. But even energy-dense lithium-ion batteries have limitations, says Xiaobing Liu, who leads the Thermal Energy Storage Group at Oak Ridge National Laboratory (ORNL). Batteries that can hold large amounts of energy are large and expensive, requiring a substantial investment to install. They gradually lose capacity with each discharge-and-recharge cycle, and they can be fire hazards. The raw materials needed to build lithium-ion batteries are also difficult to come by, and mining those minerals raises environmental and human rights issues.

“It’s a rare material, and lots of places need batteries,” Liu said. “Electric cars need lots of batteries, laptops need lots of batteries. So there’s strong competition for the materials, especially if electric cars become more and more popular.”

That’s why interest in unconventional solutions for energy storage has taken off in recent years. Companies have looked into pumped hydroelectric systems that generate electricity from water flowing out of large artificial reservoirs, underground caverns that store hydrogen fuel electrolyzed from water, elevators that lift blocks of concrete and harvest their potential energy as they fall. Some companies have landed on thermal storage.

Storing Energy in Air and Sand
The mixed-use development in Colchester will operate its own microgrid, which gets electricity from an 8-megawatt solar farm on its property. Excess energy generated by the solar farm during the day will be stored in Cheesecake Energy’s thermal energy storage system and accessed during the evening by local businesses and residents.

Here’s how it works: During the day, Cheesecake’s system takes the excess electricity and uses it to turn a motor. The motor drives a piston that compresses air, which gets hot as it’s compressed. The system then wicks off the heat from the compressed air and stores the heat in silos of sand or gravel. The compressed air, now cool and easier to store, is housed in a large air tank.

Cheesecake’s cofounder likes to use a bathtub analogy when comparing the company’s technology to lithium-ion batteries. Energy storage has two main factors—how fast it can be charged and discharged (the spigot) and how much total energy it can hold (the bathtub). Batteries have a powerful spigot, but that comes at the cost of a small tub.

“The bathtub is cheap for us, so when it comes to how much we can store, we can increase that capacity quite cheaply,” says Michael Simpson, the cofounder of Cheesecake Energy. “For batteries, it’s quite expensive to make the bath bigger.”

When it comes time to generate electricity, Cheesecake runs the system in reverse. The compressed air, once heated, drives a piston that runs a generator to produce electricity. The whole system, which can hold five to 12 hours’ worth of electricity discharging at full power, costs a half-million pounds. An additional set of shipping containers will double the storage capacity, and so on.

Don’t Waste Heat Energy
While Cheesecake’s system is primarily an electricity-in, electricity-out storage device, there are other thermal energy storage companies that specialize in releasing stored energy as heat. It’s a somewhat overlooked form of energy, but critically important—energy in the form of heat is how half of the total energy use in the world is consumed, as much as electricity and transportation combined.

A large part of that is due to industrial use by large, energy-hungry industries such as steelmaking, chemical manufacturing, and construction. The startup Kyoto Group, based in Norway, is targeting this industrial use of heat with their thermal storage system, which stores energy in the form of molten salt. Their system can take electricity or heat as input and releases hot air or steam in the range of 170 to 400 degrees Celsius as output. That temperature delivery is well suited for the food industry and paper industry, which have tested pilots of Kyoto’s system. One molten salt thermal-storage device installed at a power station outside Aalborg, Denmark stores electricity from the grid when it’s cheap and releases steam at 180 degrees Celsius to provide heat to the local district heating system.

Buildings are another big consumer of heat, accounting for almost half of total heat consumption, mostly for space and water heating. They also consume 75 percent of all electricity used. That’s why Liu’s Thermal Energy Storage Group at ORNL is focused primarily on buildings. The group’s vision is for more and more buildings to eventually include thermal storage systems. The group is researching ways to integrate thermal storage systems directly into existing building infrastructure like roofs, walls, and floors in ways that don’t take up a lot of space.

Liu hopes thermal energy storage will eventually be as ubiquitous as air conditioners, but he says it will probably take a lot more time because the benefits of the investment are not as obvious. Whereas early adopters of air conditioning could see direct benefits from investing in one (staying cool during hot summer months), home and building owners of thermal storage systems may not.

“Getting a 100 percent decarbonized grid is nothing but an optimization problem.”—Matthew Irvin, Maplewell

Commercial customers may see benefits first—they have to pay a demand charge during times when there’s high overall demand on the grid, so they can save money by pulling energy from thermal storage at those times. But Liu says residential customers don’t see demand charges. Instead, the benefits from thermal energy storage investments go to utility companies by helping take some pressure off the grid.

He says widespread adoption of thermal energy storage may have to be driven by external forces, like the government or utility companies introducing time-of-use rates for residential customers. If a substantial amount of solar or wind energy is on the grid, customers would be able to save money by purchasing and storing electricity during low-rate times.

“California already has this kind of time-of-use rate from electricity generated by solar or wind,” says Liu. “So that may create this need for storage…And then there will be a competition between thermal and electric [storage].”

Heating Up the Grid
On some level, getting value from energy storage systems is an optimization problem. When does it make sense to buy electricity directly from the grid? And when is it best to pull from storage reserves or purchase extra grid electricity to store? Maplewell Energy, a Colorado-based company that makes software that automates these decisions, hopes to make that easy for commercial customers. The software pulls data from different sources—weather reports, utility companies, and records of past energy use—to predict what to do to get the best price for electricity overall.

The company recently piloted its software at an enterprise convenience store, using the convenience store’s own refrigeration system as a type of thermal energy storage. Commercial refrigerators are required to be kept below 40 degrees Fahrenheit, but they have a wiggle room of a few degrees above freezing to play with. Before 4 pm local time, when overall demand from the local grid is highest and most expensive, Maplewell’s software instructs the store’s refrigerators to cool down to the lower end of the threshold so the store can avoid purchasing energy for refrigeration during the peak period.

“For batteries, it’s quite expensive to make the bath bigger.”—Michael Simpson, Cheesecake Energy

Matthew Irvin, the CEO of Maplewell, believes optimization software like this can help with concerns that the grid will run out of capacity trying to support a full transition from fossil fuels to electrification.

“Getting a 100 percent decarbonized grid is nothing but an optimization problem,” says Irvin.

The companies and researchers working on thermal energy storage are optimistic about their technology. If it succeeds, thermal storage devices could help consumers buffer against fluctuations in renewable energy supply and prevent overloading the grid during periods of high demand, all while using materials that are environmentally friendly, simple, and cheap.

But the space is still young. Both Cheesecake Energy and Kyoto Group were founded in 2016, Maplewell Energy in 2019, and even the ORML’s Thermal Energy Storage Group was formed only in December 2022. Companies still struggle with limited public awareness of the technology, and it takes time to scale up from building pilot systems to manufacturing thermal storage products on a large scale. Tim de Haas, the chief commercial officer for Kyoto Group, said the industry also faces regulatory and policy challenges.

But there’s also a growing demand for effective energy storage solutions. Cheesecake’s Simpson said the company’s target customers include those wanting to build new offices or factories but can’t because the local grid is at capacity.

“We’re having real issues in the U.K., where developers want to build housing estates or new commercial developments, and they’re basically told, ‘You can have enough power for that in 2030,’” says Simpson. “The grid isn’t moving fast enough for them.”
',
 'Tammy Xu',
 'IEEE Spectrum',
 'Intermediate', 0, NULL, '2026-03-18 10:21:00', '2026-03-18 10:21:00'),
 
(22, 'Mechanical Engineering',
 'Humanoid Robots Are Getting to Work',
 'humanoid-robots-are-getting-to-work',
 'Ten years ago, at the DARPA Robotics Challenge (DRC) Trial event near Miami, I watched the most advanced humanoid robots ever built struggle their way through a scenario inspired by the Fukushima nuclear disaster. A team of experienced engineers controlled each robot, and overhead safety tethers kept them from falling over. The robots had to demonstrate mobility, sensing, and manipulation—which, with painful slowness, they did.

These robots were clearly research projects, but DARPA has a history of catalyzing technology with a long-term view. The DARPA Grand and Urban Challenges for autonomous vehicles, in 2005 and 2007, formed the foundation for today’s autonomous taxis. So, after DRC ended in 2015 with several of the robots successfully completing the entire final scenario, the obvious question was: When would humanoid robots make the transition from research project to a commercial product?

The answer seems to be 2024, when a handful of well-funded companies will be deploying their robots in commercial pilot projects to figure out whether humanoids are really ready to get to work.

One of the robots that made an appearance at the DRC Finals in 2015 was called ATRIAS, developed by Jonathan Hurst at the Oregon State University Dynamic Robotics Laboratory. In 2015, Hurst cofounded Agility Robotics to turn ATRIAS into a human-centric, multipurpose, and practical robot called Digit. Approximately the same size as a human, Digit stands 1.75 meters tall (about 5 feet, 8 inches), weighs 65 kilograms (about 140 pounds), and can lift 16 kg (about 35 pounds). Agility is now preparing to produce a commercial version of Digit at massive scale, and the company sees its first opportunity in the logistics industry, where it will start doing some of the jobs where humans are essentially acting like robots already.

Are humanoid robots useful?
“We spent a long time working with potential customers to find a use case where our technology can provide real value, while also being scalable and profitable,” Hurst says. “For us, right now, that use case is moving e-commerce totes.” Totes are standardized containers that warehouses use to store and transport items. As items enter or leave the warehouse, empty totes need to be continuously moved from place to place. It’s a vital job, and even in highly automated warehouses, much of that job is done by humans.

Agility says that in the United States, there are currently several million people working at tote-handling tasks, and logistics companies are having trouble keeping positions filled, because in some markets there are simply not enough workers available. Furthermore, the work tends to be dull, repetitive, and stressful on the body. “The people doing these jobs are basically doing robotic jobs,” says Hurst, and Agility argues that these people would be much better off doing work that’s more suited to their strengths. “What we’re going to have is a shifting of the human workforce into a more supervisory role,” explains Damion Shelton, Agility Robotics’ CEO. “We’re trying to build something that works with people,” Hurst adds. “We want humans for their judgment, creativity, and decision-making, using our robots as tools to do their jobs faster and more efficiently.”

For Digit to be an effective warehouse tool, it has to be capable, reliable, safe, and financially sustainable for both Agility and its customers. Agility is confident that all of this is possible, citing Digit’s potential relative to the cost and performance of human workers. “What we’re encouraging people to think about,” says Shelton, “is how much they could be saving per hour by being able to allocate their human capital elsewhere in the building.” Shelton estimates that a typical large logistics company spends at least US $30 per employee-hour for labor, including benefits and overhead. The employee, of course, receives much less than that.

Agility is not yet ready to provide pricing information for Digit, but we’re told that it will cost less than $250,000 per unit. Even at that price, if Digit is able to achieve Agility’s goal of minimum 20,000 working hours (five years of two shifts of work per day), that brings the hourly rate of the robot to $12.50. A service contract would likely add a few dollars per hour to that. “You compare that against human labor doing the same task,” Shelton says, “and as long as it’s apples to apples in terms of the rate that the robot is working versus the rate that the human is working, you can decide whether it makes more sense to have the person or the robot.”

Agility’s robot won’t be able to match the general capability of a human, but that’s not the company’s goal. “Digit won’t be doing everything that a person can do,” says Hurst. “It’ll just be doing that one process-automated task,” like moving empty totes. In these tasks, Digit is able to keep up with (and in fact slightly exceed) the speed of the average human worker, when you consider that the robot doesn’t have to accommodate the needs of a frail human body.

Amazon’s experiments with warehouse robots
The first company to put Digit to the test is Amazon. In 2022, Amazon invested in Agility as part of its Industrial Innovation Fund, and late last year Amazon started testing Digit at its robotics research and development site near Seattle, Wash. Digit will not be lonely at Amazon—the company currently has more than 750,000 robots deployed across its warehouses, including legacy systems that operate in closed-off areas as well as more modern robots that have the necessary autonomy to work more collaboratively with people. These newer robots include autonomous mobile robotic bases like Proteus, which can move carts around warehouses, as well as stationary robot arms like Sparrow and Cardinal, which can handle inventory or customer orders in structured environments. But a robot with legs will be something new.

“What’s interesting about Digit is because of its bipedal nature, it can fit in spaces a little bit differently,” says Emily Vetterick, director of engineering at Amazon Global Robotics, who is overseeing Digit’s testing. “We’re excited to be at this point with Digit where we can start testing it, because we’re going to learn where the technology makes sense.”

Where two legs make sense has been an ongoing question in robotics for decades. Obviously, in a world designed primarily for humans, a robot with a humanoid form factor would be ideal. But balancing dynamically on two legs is still difficult for robots, especially when those robots are carrying heavy objects and are expected to work at a human pace for tens of thousands of hours. When is it worthwhile to use a bipedal robot instead of something simpler?

“The people doing these jobs are basically doing robotic jobs.”
—Jonathan Hurst, Agility Robotics

“The use case for Digit that I’m really excited about is empty tote recycling,” Vetterick says. “We already automate this task in a lot of our warehouses with a conveyor, a very traditional automation solution, and we wouldn’t want a robot in a place where a conveyor works. But a conveyor has a specific footprint, and it’s conducive to certain types of spaces. When we start to get away from those spaces, that’s where robots start to have a functional need to exist.”

The need for a robot doesn’t always translate into the need for a robot with legs, however, and a company like Amazon has the resources to build its warehouses to support whatever form of robotics or automation it needs. Its newer warehouses are indeed built that way, with flat floors, wide aisles, and other environmental considerations that are particularly friendly to robots with wheels.

“The building types that we’re thinking about [for Digit] aren’t our new-generation buildings. They’re older-generation buildings, where we can’t put in traditional automation solutions because there just isn’t the space for them,” says Vetterick. She describes the organized chaos of some of these older buildings as including narrower aisles with roof supports in the middle of them, and areas where pallets, cardboard, electrical cord covers, and ergonomics mats create uneven floors. “Our buildings are easy for people to navigate,” Vetterick continues. “But even small obstructions become barriers that a wheeled robot might struggle with, and where a walking robot might not.” Fundamentally, that’s the advantage bipedal robots offer relative to other form factors: They can quickly and easily fit into spaces and workflows designed for humans. Or at least, that’s the goal.

Vetterick emphasizes that the Seattle R&D site deployment is only a very small initial test of Digit’s capabilities. Having the robot move totes from a shelf to a conveyor across a flat, empty floor is not reflective of the use case that Amazon ultimately would like to explore. Amazon is not even sure that Digit will turn out to be the best tool for this particular job, and for a company so focused on efficiency, only the best solution to a specific problem will find a permanent home as part of its workflow. “Amazon isn’t interested in a general-purpose robot,” Vetterick explains. “We are always focused on what problem we’re trying to solve. I wouldn’t want to suggest that Digit is the only way to solve this type of problem. It’s one potential way that we’re interested in experimenting with.”

The idea of a general-purpose humanoid robot that can assist people with whatever tasks they may need is certainly appealing, but as Amazon makes clear, the first step for companies like Agility is to find enough value performing a single task (or perhaps a few different tasks) to achieve sustainable growth. Agility believes that Digit will be able to scale its business by solving Amazon’s empty tote-recycling problem, and the company is confident enough that it’s preparing to open a factory in Salem, Ore. At peak production the plant will eventually be capable of manufacturing 10,000 Digit robots per year.

A menagerie of humanoids
Agility is not alone in its goal to commercially deploy bipedal robots in 2024. At least seven other companies are also working toward this goal, with hundreds of millions of dollars of funding backing them. 1X, Apptronik, Figure, Sanctuary, Tesla, and Unitree all have commercial humanoid robot prototypes.

Despite an influx of money and talent into commercial humanoid robot development over the past two years, there have been no recent fundamental technological breakthroughs that will substantially aid these robots’ development. Sensors and computers are capable enough, but actuators remain complex and expensive, and batteries struggle to power bipedal robots for the length of a work shift.

There are other challenges as well, including creating a robot that’s manufacturable with a resilient supply chain and developing the service infrastructure to support a commercial deployment at scale. The biggest challenge by far is software. It’s not enough to simply build a robot that can do a job—that robot has to do the job with the kind of safety, reliability, and efficiency that will make it desirable as more than an experiment.

There’s no question that Agility Robotics and the other companies developing commercial humanoids have impressive technology, a compelling narrative, and an enormous amount of potential. Whether that potential will translate into humanoid robots in the workplace now rests with companies like Amazon, who seem cautiously optimistic. It would be a fundamental shift in how repetitive labor is done. And now, all the robots have to do is deliver.
',
 'Evan Ackerman',
 'IEEE Spectrum',
 'Intermediate', 0, NULL, '2026-03-18 10:22:00', '2026-03-18 10:22:00'),
 
(23, 'Mechanical Engineering',
 'Robots at Work: Where Do We Fit?',
 'robots-at-work-where-do-we-fit',
 'The robots are coming for our jobs—and sooner than we think. That’s the gist of a number of recent reports by economists and technology researchers. For instance, nearly half of all U.S. jobs could be automated within a decade or two, cautions a study by an Oxford University economist and engineer. Smart machines will replace one in three jobs by 2025, warns technology research firm Gartner. Robots will perform 45 percent of all factory tasks by 2025, up from 10 percent today, blares Bank of America.

We have been down this road before, other economists fire back. Two hundred years ago, English textile workers felt so threatened by power looms that they started smashing machinery. Worries like those of the Luddites also arose when mechanization scythed through farm hands, when automation first threatened factory workers, and when PCs began to eliminate secretarial jobs. Every time, productivity grew, the economy thrived, and employment rose. Why wouldn’t that be the case today?

“What’s new is that algorithms are sensing things and reacting almost as well as a human would,” said W. Brian Arthur, a visiting researcher at the Intelligent Systems Lab at Palo Alto Research Center, whose theories shaped our understanding of the high-tech economy. “We’re living in a world where, for the first time in human history, we can get a lot done, not just in manufacturing but in the service economy, extraordinarily cheaply and automatically.”

Algorithms have already eliminated millions of jobs among factory workers, video store clerks, travel agents, bookkeepers, and secretaries. Middle-skill occupations, which require more schooling or training than high school but less than a four-year college, fell from 60 percent of all U.S. jobs in 1979 to 46 percent in 2012. Similar declines occurred in 16 European economies.

Algorithms running on interconnected computers have reshaped entire industries. Arthur points to the Blockbuster video chain: “It doesn’t employ fewer people, it’s gone. All the travel agents that populated Palo Alto have disappeared.”

Now algorithms are invading the skilled professions. Software is replacing some loan officers, attorneys, and sports and business journalists who write news. IBM is modifying its Jeopardy-winning Watson technology to diagnose diseases and read medical images. And engineers increasingly rely on expert systems to assess designs and simulations.

In the past, when mechanization disrupted farming, laborers took on factory jobs. When factory employment flattened, workers moved into offices. “Today,” Arthur said, “we don’t have a sector that is growing fast enough to mop up those people who get laid off.”

The Second Economy
Five years ago, Arthur coined the phrase “the second economy” to describe a system in which Internet-enabled computers execute business processes once handled by people.

Twenty years ago, for example, when we wanted to travel we called travel agents, who would ask where and when we wanted to go, query some proprietary databases (or even paper catalogs), talk us through our options, and book reservations.

Today, we simply go online. This sets off a conversation among machines. Software gathers information about available flights. It charges our credit card, checks our preferences, reserves our seat, and sees if we qualify for a security clearance or lounge access. It adds our weight and baggage to the flight manifest, and orders additional fuel for the flight.

Everything takes place within seconds, without any human intervention. Similar conversations are happening everywhere in the economy—between RFID tags and scanners at ports and warehouses, between television sets and servers that stream movies, and more.

“We’re undergoing a digital revolution, a transformation of the economy comparable to the Industrial Revolution. The Internet of Things is creating masses of digital sensors, and they are going to generate masses of data,” Arthur said. As new algorithms arise to make sense of the data, they will only strengthen the second economy.

This transformation is already having a profound effect on people’s employment prospects. And its impact is growing because computers are getting smarter, quickly.

Only a few years ago, for example, voice recognition worked in highly structured dialogues, where it expected certain types of responses. Today, Apple’s Siri and its competitors provide (mostly) relevant answers to unstructured questions. Some apps even translate languages on the fly.

Autonomous cars show just how fast AI can evolve. In a 2004 DARPA challenge, the best car chugged only seven miles down a straight road. Three years later, six autonomous vehicles completed a 60-mile circuit through an abandoned military base among moving cars, pedestrians, and street signs. Their performance was not as good as a human. This led Silicon Valley entrepreneur Martin Ford to predict that AI was not likely to replace human truck drivers anytime soon.

One year later in 2010, Google announced that its fleet of autonomous cars had logged 100,000 incident-free miles. Today, nearly every large auto company has an autonomous car program. This past May, Nevada granted the first commercial license ever to an autonomous truck.

Ford, meanwhile, appears to have reconsidered the speed of change. He named his most recent book, Rise of the Robots: Technology and the Threat of a Jobless Future.

Getting Smarter Faster
According to Gill Pratt, who spearheaded DARPA’s robot challenge and now heads Toyota’s $1 billion robotics program, two emerging technologies will help robots learn even faster.

The first is cloud robotics. In the past, memory and processor speed limited robots’ ability to learn. Today, robots can upload what they learn into the cloud. Once there, other robots could access instructions for everything from cooking chicken cordon bleu to performing surgery. What one robot knows, every robot can know.

The second is deep learning, an advanced type of machine learning that allows robots to learn things that humans understand tacitly. Robots, for example, have trouble telling tables from chairs. Both may have the same number of legs, similar surface areas, and stand tall or short. Yet humans usually know where to sit and where to place their drinks.

Deep learning tries to overcome this problem with algorithms that sort through vast amounts of data and come to their own conclusions. Google, for example, used deep learning software to scour YouTube cat videos and come to its own conclusions about what defined a cat. It used this algorithm to identify cats twice as well as any other image recognition software. It took Google only 16,000 computers and 10 million videos to learn to do this.

Compared with even the youngest children, who know a cat when they see one, such results may seem pathetic. Yet Google learned enough from its experiment to improve its search engine, slash translation errors, and provide more relevant newsfeeds. Recently, a Google deep learning program, AlphaGo, soundly defeated the reigning European champion of the game Go, a feat that most AI specialists expected to take another decade.

Pratt imagines a world where robots and distributed sensors would send data to the cloud. Deep-learning AI would then analyze the data and use it to make robots and other types of AI software smarter. In this way the combination of cloud robotics and deep learning could yield rapid advances in machine intelligence, and displace many workers in a very short time.

This may already be happening. In 2011, Erik Brynjolfsson and Andrew McAfee of MIT’s Sloan School of Management warned in their book Racing against the Machine about technology’s potential for disruption.

Building on Arthur’s research, they noted that between 1947 and 2000, automation increased productivity, employment, and wages. Since 2000, however, U.S. productivity continued rising, but new job creation slowed and median income actually declined. They see smart machines at work. “It’s not so much a matter of jobs in general as specific types of skills being substituted for by new technologies. People with those skills see falling demand for their labor, so they will have lower incomes and less work unless they develop new skills,” McAfee said.

Typically, the easiest tasks to automate are routine and repetitive, such as classifying information, routing files, or operating a metal press. On the other hand, jobs for restaurant workers, janitors, and home health aides are growing. They require few skills, which keeps wages low, but they involve multiple tasks and human interaction that are difficult to automate.

The true winners in the new economy have specialized skills and often use computers to amplify their knowledge and capabilities. They are the ones, for example, who create software to book the lowest fares or prepare taxes. Their companies may make billions of dollars, but their websites and software put hundreds of thousands of people doing routine jobs out of work.

“Two hundred years ago the Industrial Revolution replaced people and animal power with machines,” Arthur said. “Now we are developing a neural system to go with it. It is a huge and unstoppable transformation.”

The Technology Job Machine
Others are more optimistic. They believe that technology will spur employment, just as it always has.

In 2015, three economists from international management consultant Deloitte defended that view in a paper, “Jobs and People: The Great Job-Creating Machine,” which was short-listed for the Society of Business Economists’ top honor, the Rybczynski Prize. Machines “seem no closer to eliminating the need for human labor than at any time in the last 150 years,” the authors wrote.

“The problem is that, while it is easy to point to jobs lost due to technology, it is not as easy to identify jobs created by technology,” Alex Cole, one of the authors, said.

Cole and his fellow researchers found those jobs by analyzing labor data going back to 1871. Some of their findings were not surprising. Machines replaced muscle on farms and in factories, while employment grew among people who create, implement, and maintain technology.

Employment also rose for those with specialized knowledge. For example, Britain’s 1871 census recorded only 28,000 nurses. They held low-skilled positions closer to domestic service than medicine. With better training, the value of nurses increased and their numbers swelled to 300,000 in 2014.

Demand also surged for service and caring jobs like hairdressers and bar staff. Cole attributes this to technology.

Why? The ability of technology to raise productivity slashed the cost of many necessities, Cole said. In 1950, for example, food made up 35 percent of what the average Briton spent on essential goods and services. By 2014, food’s share had fallen to 11 percent. Meanwhile, the real cost of U.K. cars fell by half over the past 25 years, while the U.S. cost of televisions plummeted 98 percent since 1950.

Technology-driven price decreases give consumers more money to spend. Over the past 20 years, they increasingly spent it on health (think nurses), education (more teachers and teacher’s aides), and services that were once considered a luxury, Cole said. In 1871, for example, there was only one hairdresser per 1,800 people; today there are more than six. Because Britons can afford to go out more frequently, the number of bar staff has quadrupled since 1951. Other researchers have reached similar conclusions.

Nevertheless, these studies can be misleading: If smart machines are truly game changers, then past trends and historical data say little about the future. For that reason, leading MIT labor economist David Autor looks instead at the inherent limitations of smart machines in an aptly named 2015 paper, "Why Are There Still So Many Jobs?"

For example, why do robots install car windshields in factories, but humans repair them when they break, Autor asks. The answer is that robots require controlled environments, while humans, who are more flexible, can cope with unstructured tasks. That same adaptability is essential for medical technicians, plumbers, electricians, and many other middle-skill jobs.

Autor is also underwhelmed by machine learning, finding it cumbersome and often riddled with surprising errors. Humans leave computers scratching their digital heads when it comes to identifying cats or not sitting on tables. Even IBM’s Watson, which trounced the world’s best Jeopardy player, got one question spectacularly wrong, he noted. Asked which U.S. city named two airports for a military hero and a battle, it named a city in Canada—Toronto. No one wants a self-driving car to make a similar mistake.

Jobs on the Rebound?
Although they disagree about the long-term impacts, nearly all experts agree that smart machines are creating a very different job market. Autor and like-minded optimists expect pressures on middle skill jobs to eventually reverse because these jobs combine not only knowledge, but also adaptability, problem solving, common sense, and the ability to communicate with other people.

“Many of the tasks currently bundled into these jobs cannot readily be unbundled—with machines performing the middle-skill tasks and workers performing only a low-skill residual—without a substantial drop in quality,” Autor has written.

Businesses are already pairing human flexibility with mechanical precision. In 2012, for example, Amazon bought warehouse robot developer Kiva Systems. Kiva’s robots work alongside people, bringing shelves of parts to workers. The robots even use laser pointers to show workers which parts to pick, but only humans are flexible enough to rapidly manipulate and wrap parts for shipment

Arthur is not as sanguine. The growth of the second economy is great for people plugged into the system or working with robots. Meanwhile, those without the right skills are finding it harder to secure good, full-time jobs.

“It’s not just that we’ve lost jobs, but that the middle class has steadily lost hope that life would become better. In America, it was always taken for granted that the next generation would do better. Many people cannot take that for granted anymore,” Arthur said.

“It’s going to be a large social problem over the next 20 or 30 years,” Arthur said. “We’ll solve it, because as human beings we always solve our problems with new institutions and new types of arrangements. But until that day, it is not going to be easy.”
',
 'Alan S. Brown',
 'ASME',
 'Advanced', 0, NULL, '2026-03-18 10:23:00', '2026-03-18 10:23:00'),
 
(24, 'Mechanical Engineering',
 'Nuclear Fusion''s New Idea: An Off-the-Shelf Stellarator',
 'nuclear-fusions-new-idea-off-the-shelf-stellarator',
 'For a machine that’s designed to replicate a star, the world’s newest stellarator is a surprisingly humble-looking apparatus. The kitchen-table-size contraption sits atop stacks of bricks in a cinder-block room at the Princeton Plasma Physics Laboratory (PPPL) in Princeton, N.J., its parts hand-labeled in marker.

The PPPL team invented this nuclear-fusion reactor, completed last year, using mainly off-the-shelf components. Its core is a glass vacuum chamber surrounded by a 3D-printed nylon shell that anchors 9,920 meticulously placed permanent rare-earth magnets. Sixteen copper-coil electromagnets resembling giant slices of pineapple wrap around the shell crosswise.

The arrangement of magnets forms the defining feature of a stellarator: an entirely external magnetic field that directs charged particles along a spiral path to confine a superheated plasma. Within this enigmatic fourth state of matter, atoms that have been stripped of their electrons collide, their nuclei fusing and releasing energy in the same process that powers the sun and other stars. Researchers hope to capture this energy and use it to produce clean, zero-carbon electricity.

PPPL’s new reactor is the first stellarator built at this government lab in 50 years. It’s also the world’s first stellarator to employ permanent magnets, rather than just electromagnets, to coax plasma into an optimal three-dimensional shape. Costing only US $640,000 and built in less than a year, the device stands in contrast to prominent stellarators like Germany’s Wendelstein 7-X, a massive, tentacled machine that took $1.1 billion and more than 20 years to construct.

PPPL researchers say their simpler machine demonstrates a way to build stellarators far more cheaply and quickly, allowing researchers to easily test new concepts for future fusion power plants. The team’s use of permanent magnets may not be the ticket to producing commercial-scale energy, but PPPL’s accelerated design-build-test strategy could crank out new insights on plasma behavior that could push the field forward more rapidly.

Indeed, the team’s work has already spurred the formation of two stellarator startups that are testing their own PPPL-inspired designs, which their founders hope will lead to breakthroughs in the quest for fusion energy.

Are Stellarators the Future of Nuclear Fusion?
The pursuit of energy production through nuclear fusion is considered by many to be the holy grail of clean energy. And it’s become increasingly important as a rapidly warming climate and soaring electricity demand have made the need for stable, carbon-free power ever more acute. Fusion offers the prospect of a nearly limitless source of energy with no greenhouse gas emissions. And unlike conventional nuclear fission, fusion comes with no risk of meltdowns or weaponization, and no long-lived nuclear waste.

Fusion reactions have powered the sun since it formed an estimated 4.6 billion years ago, but they have never served to produce usable energy on Earth, despite decades of effort. The problem isn’t whether fusion can work. Physics laboratories and even a few individuals have successfully fused the nuclei of hydrogen, liberating energy. But to produce more power than is consumed in the process, simply fusing atoms isn’t enough.

The past few years have brought eye-opening advances from government-funded fusion programs such as PPPL and the Joint European Torus, as well as private companies. Enabled by gains in high-speed computing, artificial intelligence, and materials science, nuclear physicists and engineers are toppling longstanding technical hurdles. And stellarators, a once-overlooked approach, are back in the spotlight.

“Stellarators are one of the most active research areas now, with new papers coming out just about every week,” says Scott Hsu, the U.S. Department of Energy’s lead fusion coordinator. “We’re seeing new optimized designs that we weren’t capable of coming up with even 10 years ago. The other half of the story that’s just as exciting is that new superconductor technology and advanced manufacturing capabilities are making it more possible to actually realize these exquisite designs.”

Why Is Plasma Containment Important in Fusion Energy?
For atomic nuclei to fuse, the nuclei must overcome their natural electrostatic repulsion. Extremely high temperatures—in the millions of degrees—will get the particles moving fast enough to collide and fuse. Deuterium and tritium, isotopes of hydrogen with, respectively, one and two neutrons in their nuclei, are the preferred fuels for fusion because their nuclei can overcome the repulsive forces more easily than those of heavier atoms.

Heating these isotopes to the required temperatures strips electrons from the atomic nuclei, forming a plasma: a maelstrom of positively charged nuclei and negatively charged electrons. The trick is keeping that searingly hot plasma contained so that some of the nuclei fuse.

Currently, there are two main approaches to containing plasma. Inertial confinement uses high-energy lasers or ion beams to rapidly compress and heat a small fuel pellet. Magnetic confinement uses powerful magnetic fields to guide the charged particles along magnetic-field lines, preventing these particles from drifting outward.

Many magnetic-confinement designs—including the $24.5 billion ITER reactor under construction since 2010 in the hills of southern France—use an internal current flowing through the plasma to help to shape the magnetic field. But this current can create instabilities, and even small instabilities in the plasma can cause it to escape confinement, leading to energy losses and potential damage to the hardware.

Stellarators like PPPL’s are a type of magnetic confinement, with a twist.

How the Stellarator Was Born
Located at the end of Stellarator Road and a roughly 5-kilometer drive from Princeton University’s leafy campus, PPPL is one of 17 U.S. Department of Energy labs, and it employs about 800 scientists, engineers, and other workers. Hanging in PPPL’s lobby is a black-and-white photo of the lab’s founder, physicist Lyman Spitzer, smiling as he shows off the fanciful-looking apparatus he invented and dubbed a stellarator, or “star generator.”

According to the lab’s lore, Spitzer came up with the idea while riding a ski lift at Aspen Mountain in 1951. Enrico Fermi had observed that a simple toroidal, or doughnut-shaped, magnetic-confinement system wouldn’t be sufficient to contain plasma for nuclear fusion because the charged particles would drift outward and escape confinement.

“This technology is designed to be a stepping stone toward a fusion power plant.”

Spitzer determined that a figure-eight design with external magnets could create helical magnetic-field lines that would spiral around the plasma and more efficiently control and contain the energetic particles. That configuration, Spitzer reasoned, would be efficient enough that it wouldn’t require large currents running through the plasma, thus reducing the risk of instabilities and allowing for steady-state operation.

“In many ways, Spitzer’s brilliant idea was the perfect answer” to the problems of plasma confinement, says Steven Cowley, PPPL’s director since 2018. “The stellarator offered something that other approaches to fusion energy couldn’t: a stable plasma field that can sustain itself without any internal current.”

Spitzer’s stellarator quickly captured the imagination of midcentury nuclear physicists and engineers. But the invention was ahead of its time.

Tokamaks vs. Stellarators
The stellarator’s lack of toroidal symmetry made it challenging to build. The external magnetic coils needed to be precisely engineered into complex, three-dimensional shapes to generate the twisted magnetic fields required for stable plasma confinement. In the 1950s, researchers lacked the high-performance computers needed to design optimal three-dimensional magnetic fields and the engineering capability to build machines with the requisite precision.

Meanwhile, physicists in the Soviet Union were testing a new configuration for magnetically confined nuclear fusion: a doughnut-shaped device called a tokamak—a Russian acronym that stands for “toroidal chamber with magnetic coils.” Tokamaks bend an externally applied magnetic field into a helical field inside by sending a current through the plasma. They seemed to be able to produce plasmas that were hotter and denser than those produced by stellarators. And compared with the outrageously complex geometry of stellarators, the symmetry of the tokamaks’ toroidal shape made them much easier to build.

Following the lead of other nations’ fusion programs, the DOE shifted most of its fusion resources to tokamak research. PPPL converted Spitzer’s Model C stellarator into a tokamak in 1969.

Since then, tokamaks have dominated fusion-energy research. But by the late 1980s, the limitations of the approach were becoming more apparent. In particular, the currents that run through a tokamak’s plasma to stabilize and heat it are themselves a source of instabilities as the currents get stronger.

To force the restive plasma into submission, the geometrically simple tokamaks need additional features that increase their complexity and cost. Advanced tokamaks—there are about 60 currently operating—have systems for heating and controlling the plasma and massive arrays of magnets to create the confining magnetic fields. They also have cryogenics to cool the magnets to superconducting temperatures a few meters away from a 150 million °C plasma.

Tokamaks thus far have produced energy only in short pulses. “After 70 years, nobody really has even a good concept for how to make a steady-state tokamak,” notes Michael Zarnstorff, a staff research physicist at PPPL. “The longest pulse so far is just a few minutes. When we talk to electric utilities, that’s not actually what they want to buy.”

Computational Power Revives the Stellarator
With tokamaks gobbling up most of the world’s public fusion-energy funds, stellarator research lay mostly dormant until the 1980s. Then, some theorists started to put increasingly powerful computers to work to help them optimize the placement of magnetic coils to more precisely shape the magnetic fields.

The effort got a boost in 1981, when then-PPPL physicist Allen Boozer invented a coordinate system—known in the physics community as Boozer coordinates—that helps scientists understand how different configurations of magnets affect magnetic fields and plasma confinement. They can then design better devices to maintain stable plasma conditions for fusion. Boozer coordinates can also reveal hidden symmetries in the three-dimensional magnetic-field structure, which aren’t easily visible in other coordinate systems. These symmetries can significantly improve plasma confinement, reduce energy losses, and make the fusion process more efficient.

“We’re seeing new optimized designs we weren’t capable of coming up with 10 years ago.”

“The accelerating computational power finally allowed researchers to challenge the so-called fatal flaw of stellarators: the lack of toroidal symmetry,” says Boozer, who is now a professor of applied physics at Columbia University.

The new insights gave rise to stellarator designs that were far more complex than anything Spitzer could have imagined [see sidebar, “Trailblazing Stellarators”]. Japan’s Large Helical Device came online in 1998 after eight years of construction. The University of Wisconsin’s Helically Symmetric Experiment, whose magnetic-field coils featured an innovative quasi-helical symmetry, took nine years to build and began operation in 1999. And Germany’s Wendelstein 7-X—the largest and most advanced stellarator ever built—produced its first plasma in 2015, after more than 20 years of design and construction.

Experiment Failure Leads to New Stellarator Design
In the late 1990s, PPPL physicists and engineers began designing their own version, called the National Compact Stellarator Experiment (NCSX). Envisioned as the world’s most advanced stellarator, it employed a new magnetic-confinement concept called quasi-axisymmetry—a compromise that mimics the symmetry of a tokamak while retaining the stability and confinement benefits of a stellarator by using only externally generated magnetic fields.

“We tapped into every supercomputer we could find,” says Zarnstorff, who led the NCSX design team, “performing simulations of hundreds of thousands of plasma configurations to optimize the physics properties.”

But the design was, like Spitzer’s original invention, ahead of its time. Engineers struggled to meet the precise tolerances, which allowed for a maximum variation from assigned dimensions of only 1.5 millimeters across the entire device. In 2008, with the project tens of millions of dollars over budget and years behind schedule, NCSX was canceled. “That was a very sad day around here,” says Zarnstorff. “We got to build all the pieces, but we never got to put it together.”

Now, a segment of the NCSX vacuum vessel—a contorted hunk made from the superalloy Inconel—towers over a lonely corner of the C-Site Stellarator Building on PPPL’s campus. But if its presence is a reminder of failure, it is equally a reminder of the lessons learned from the $70 million project.

For Zarnstorff, the most important insights came from the engineering postmortem. Engineers concluded that, even if they had managed to successfully build and operate NCSX, it was doomed by the lack of a viable way to take the machine apart for repairs or reconfigure the magnets and other components.

With the experience gained from NCSX and PPPL physicists’ ongoing collaborations with the costly, delay-plagued Wendelstein 7-X program, the path forward became clearer. “Whatever we built next, we knew we needed to make it less expensively and more reliably,” says Zarnstorff. “And we knew we needed to build it in a way that would allow us to take the thing apart.”

A Testbed for Fusion Energy
In 2014, Zarnstorff began thinking about building a first-of-its-kind stellarator that would use permanent magnets, rather than electromagnets, to create its helical field, while retaining electromagnets to shape the toroidal field. (Electromagnets generate a magnetic field when an electric current flows through them and can be turned on or off, whereas permanent magnets produce a constant magnetic field without needing an external power source.)

Even the strongest permanent magnets wouldn’t be capable of confining plasma robustly enough to produce commercial-scale fusion power. But they could be used to create a lower-cost experimental device that would be easier to build and maintain. And that, crucially, would allow researchers to easily adjust and test magnetic fields that could inform the path to a power-producing device.

PPPL dubbed the device Muse. “Muse was envisioned as a testbed for innovative magnetic configurations and improving theoretical models,” says PPPL research physicist Kenneth Hammond, who is now leading the project. “Rather than immediate commercial application, it’s more focused on exploring fundamental aspects of stellarator design and plasma behavior.”

The Muse team designed the reactor with two independent sets of magnets. To coax charged particles into a corkscrew-like trajectory, small permanent neodymium magnets are arranged in pairs and mounted to a dozen 3D-printed panels surrounding the glass vacuum chamber, which was custom-made by glass blowers. Adjacent rows of magnets are oriented in opposite directions, twisting the magnetic-field lines at the outside edges.

Outside the shell, 16 electromagnets composed of circular copper coils generate the toroidal part of the magnetic field. These very coils were mass-produced by PPPL in the 1960s, and they have been a workhorse for rapid prototyping in numerous physics laboratories ever since.

“In terms of its ability to confine particles, Muse is two orders of magnitude better than any stellarator previously built,” says Hammond. “And because it’s the first working stellarator with quasi-axisymmetry, we will be able to test some of the theories we never got to test on NCSX.”

The neodymium magnets are a little bigger than a button magnet that might be used to hold a photo to a refrigerator door. Despite their compactness, they pack a remarkable punch. During my visit to PPPL, I turned a pair of magnets in my hands, alternating their polarities, and found it difficult to push them together and pull them apart.

Graduate students did the meticulous work of placing and securing the magnets. “This is a machine built on pizza, basically,” says Cowley, PPPL’s director. “You can get a lot out of graduate students if you give them pizza. There may have been beer too, but if there was, I don’t want to know about it.”

The Muse project was financed by internal R&D funds and used mostly off-the-shelf components. “Having done it this way, I would never choose to do it any other way,” Zarnstorff says.

Stellarex and Thea Energy Advance Stellarator Concepts
Now that Muse has demonstrated that stellarators can be made quickly, cheaply, and highly accurately, companies founded by current and former PPPL researchers are moving forward with Muse-inspired designs.

Zarnstorff recently cofounded a company called Stellarex. He says he sees stellarators as the best path to fusion energy, but he hasn’t landed on a magnet configuration for future machines. “It may be a combination of permanent and superconducting electromagnets, but we’re not religious about any one particular approach; we’re leaving those options open for now.” The company has secured some DOE research grants and is now focused on raising money from investors.

Thea Energy, a startup led by David Gates, who until recently was the head of stellarator physics at PPPL, is further along with its power-plant concept. Like Muse, Thea focuses on simplified manufacture and maintenance. Unlike Muse, the Thea concept uses planar (flat) electromagnetic coils built of high-temperature superconductors.

“The idea is to use hundreds of small electromagnets that behave a lot like permanent magnets, with each creating a dipole field that can be switched on and off,” says Gates. “By using so many individually actuated coils, we can get a high degree of control, and we can dynamically adjust and shape the magnetic fields in real time to optimize performance and adapt to different conditions.”

The company has raised more than $23 million and is designing and prototyping its initial project, which it calls Eos, in Kearny, N.J. “At first, it will be focused on producing neutrons and isotopes like tritium,” says Gates. “The technology is designed to be a stepping stone toward a fusion power plant called Helios, with the potential for near-term commercialization.”

Stellarator Startup Leverages Exascale Computing
Of all the private stellarator startups, Type One Energy is the most well funded, having raised $82.5 million from investors that include Bill Gates’s Breakthrough Energy Ventures. Type One’s leaders contributed to the design and construction of both the University of Wisconsin’s Helically Symmetric Experiment and Germany’s Wendelstein 7-X stellarators.

The Type One stellarator design utilizes a highly optimized magnetic-field configuration designed to improve plasma confinement. Optimization can relax the stringent construction tolerances typically required for stellarators, making them easier and more cost-effective to engineer and build.

Type One’s design, like that of Thea Energy’s Eos, makes use of high-temperature superconducting magnets, which provide higher magnetic strength, require less cooling power, and could lower costs and allow for a more compact and efficient reactor. The magnets were designed for a tokamak, but Type One is modifying the coil structure to accommodate the intricate twists and turns of a stellarator.

In a sign that stellarator research may be moving from mainly scientific experiments into the race to field the first commercially viable reactor, Type One recently announced that it will build “the world’s most advanced stellarator” at the Bull Run Fossil Plant in Clinton, Tenn. To construct what it’s calling Infinity One—expected to be operational by early 2029—Type One is teaming up with the Tennessee Valley Authority and the DOE’s Oak Ridge National Laboratory.

“As an engineering testbed, Infinity One will not be producing energy,” says Type One CEO Chris Mowry. “Instead, it will allow us to retire any remaining risks and sign off on key features of the fusion pilot plant we are currently designing. Once the design validations are complete, we will begin the construction of our pilot plant to put fusion electrons on the grid.”

To help optimize the magnetic-field configuration, Mowry and his colleagues are utilizing Summit, one of Oak Ridge’s state-of-the-art exascale supercomputers. Summit is capable of performing more than 200 million times as many operations per second as the supercomputers of the early 1980s, when Wendelstein 7-X was first conceptualized.

AI Boosts Fusion Reactor Efficiency
Advances in computational power are already leading to faster design cycles, greater plasma stability, and better reactor designs. Ten years ago, an analysis of a million different configurations would have taken months; now a researcher can get answers in hours.

And yet, there are an infinite number of ways to make any particular magnetic field. “To find our way to an optimum fusion machine, we may need to consider something like 10 billion configurations,” says PPPL’s Cowley. “If it takes months to make that analysis, even with high-performance computing, that’s still not a route to fusion in a short amount of time.”

In the hope of shortcutting some of those steps, PPPL and other labs are investing in artificial intelligence and using surrogate models that can search and then rapidly home in on promising solutions. “Then, you start running progressively more precise models, which bring you closer and closer to the answer,” Cowley says. “That way we can converge on something in a useful amount of time.”

But the biggest remaining hurdles for stellarators, and magnetic-confinement fusion in general, involve engineering challenges rather than physics challenges, say Cowley and other fusion experts. These include developing materials that can withstand extreme conditions, managing heat and power efficiently, advancing magnet technology, and integrating all these components into a functional and scalable reactor.

Over the past half decade, the vibe at PPPL has grown increasingly optimistic, as new buildings go up and new researchers arrive on Stellarator Road to become part of what may be the grandest scientific challenge of the 21st century: enabling a world powered by safe, plentiful, carbon-free energy.

PPPL recently broke ground on a new $110 million office and laboratory building that will house theoretical and computational scientists and support the work in artificial intelligence and high-performance computing that is increasingly propelling the quest for fusion. The new facility will also provide space for research supporting PPPL’s expanded mission into microelectronics, quantum sensors and devices, and sustainability sciences.

PPPL researchers’ quest will take a lot of hard work and, probably, a fair bit of luck. Stellarator Road may be only a mile long, but the path to success in fusion energy will certainly stretch considerably farther.
',
 'Tom Clynes',
 'IEEE Spectrum',
 'Advanced', 0, NULL, '2026-03-18 10:24:00', '2026-03-18 10:24:00');
 
-- ----------------------------------------
-- Subject 5: Mechanical Engineering with Transportation (articles 25-30)
-- ----------------------------------------
INSERT INTO articles (article_id, subject, title, slug, content, author, source, level, read_count, deleted_at, created_at, updated_at) VALUES
(25, 'Mechanical Engineering with Transportation',
 'Fantasy to Reality: NASA Pushes Electric Flight Envelope',
 'fantasy-to-reality-nasa-pushes-electric-flight-envelope',
 'Cleaner, quieter, more affordable flight is a focus for NASA aeronautics researchers who are currently pursuing aircraft propulsion technologies that could soon benefit the planet, the flying public, and American industry. These efforts include overcoming several hardware challenges, while also developing enabling technologies, to take commercial electric-powered flight from the realm of hopeful fantasy to an everyday reality.

Electrified Propulsion

To that end, NASA’s work in Electrified Aircraft Propulsion (EAP) is focused on the use of electric motors and generators to help power a plane’s flight. While smaller aircraft (two-seaters and drones) have flown using all-electric systems, the agency’s EAP research team is leading the agency’s broader focus on hybrid and turboelectric systems, which combine turbine engines and electric power, under the Advanced Air Transport Technology (AATT) project.

“The technology being developed will potentially graduate to flight demonstration in NASA’s new Electrified Powertrain Flight Demonstration Project,” said Jim Heidmann, AATT manager at NASA’s Glenn Research Center in Cleveland. “We’re working with external partners to flight test a suite of electrified propulsion technologies that make them viable candidates for future commercial transport aircraft.”

Getting Down to Flight-Weight

Machines that convert fuel to electricity, drive fans, and engines can lead to new designs that reduce fuel and energy usage in aircraft. Quieter electric motors can slash noise pollution around airports. And since electrical power can be distributed more flexibly across the aircraft, more aerodynamically efficient designs are possible, which could further reduce the amount of energy used during a trip, saving fuel and cutting costs.

While the proposed savings are worth pursuing, electrified systems require additional machines, power electronics, cables, batteries, and protective systems that add significant weight to the aircraft, offsetting any benefits of an electric system. That means that the aerodynamic performance must overcome this weight, and that the new electrical systems must be more efficient even at reduced size and weight.

Getting systems flight-ready requires an array of new technologies.

“It takes about 45 megawatts (MW) to power a large commercial aircraft,” said Heidmann. “NASA is looking at concepts that use electrical power to contribute about 2.4 MW toward that total. That is enough electrical power to run a small township, to put it in perspective. There is a fuel-burn-reduction benefit if we can achieve this, but there are significant technical challenges to getting there.”

Succeeding to a Fault

Plenty of hard work and challenges remain for NASA and its many partners.

“What will drive the success of electrification in aircraft are key technologies like advanced machines, power electronics, and fault management devices,” said Amy Jankovsky, EAP technologies project manager at NASA Glenn. “And advancements in soft magnetic materials and insulation are key to all of these areas.”

Fault management provides safety for aircraft systems. Flight-ready circuit interrupters shut off if they detect dangerous faults such as overloads or electric shorts. This stops the ﬂow of electricity to or from an arcing or failing component until the problem is corrected.

“Circuit-breakers for use onboard commercial aircraft must be strong enough to stop megawatts of energy,” said Jankovsky. “They need to be able to respond in microseconds; and, perhaps the biggest challenge, they need to be ten times lighter than anything currently in existence.”

Key to any electric power system are magnets. How soft a magnet is refers to how easily it can be magnetized. For aircraft, new magnetic alloys can help reduce weight and improve efﬁciency. NASA researchers have found a way to create flexible ribbons of specially designed material that can produce a one-mile long, 5-millimeter wide ribbon of soft magnetic alloy. These custom magnetic materials have properties that can be used in speciﬁc components including power converters, motors, and sensors.

By combining NASA-engineered electric power plants and newly developed components, the environment can expect to get a healthy boost from the flying public. If NASA has its way, by the mid-2030s airports will be quieter, air will be cleaner, and flying will still be fast, efficient and, most importantly, safe.

“In a general sense, we are focused on three key areas. The environment, aircraft efficiency, and the U.S. economy,” said Heidmann. “Based on our research and the best science available, I’m convinced that electrification is a solution that can deliver a cleaner and quieter aircraft.”
',
 'Mike Giannone',
 'NASA',
 'Easy', 0, NULL, '2026-03-18 10:25:00', '2026-03-18 10:25:00'),
 
(26, 'Mechanical Engineering with Transportation',
 'Eviation''s Maiden Flight Could Usher in Electric Aviation Era',
 'eviations-maiden-flight-usher-in-electric-aviation-era',
 'The first commercial all-electric passenger plane is just weeks away from its maiden flight, according to its maker Israeli startup Eviation. If successful, the nine-seater Alice aircraft would be the most compelling demonstration yet of the potential for battery-powered flight. But experts say there’s still a long way to go before electric aircraft makes a significant dent in the aviation industry.

The Alice is currently undergoing high-speed taxi tests at Arlington Municipal Airport close to Seattle, says Eviation CEO Omer Bar-Yohay. This involves subjecting all of the plane’s key systems and fail-safe mechanisms to a variety of different scenarios to ensure they are operating as expected before its first flight. The company is five or six good weather days away from completing those tests, says Bar-Yohay, after which the plane should be cleared for takeoff. Initial flights won’t push the aircraft to its limits, but the Alice should ultimately be capable of cruising speeds of 250 knots (463 kilometers per hour) and a maximum range of 440 nautical miles (815 kilometers).

Electric aviation has received considerable attention in recent years as the industry looks to reduce its carbon emissions. And while the Alice won’t be the first all-electric aircraft to take to the skies, Bar-Yohay says it will be the first designed with practical commercial applications in mind. Eviation plans to offer three configurations—a nine-seater commuter model, a six-seater executive model for private jet customers, and a cargo version with a capacity of 12.74 cubic meters. The company has already received advance orders from logistics giant DHL and Massachusetts-based regional airline Cape Air.

The first commercial all-electric passenger plane is just weeks away from its maiden flight, according to its maker Israeli startup Eviation. If successful, the nine-seater Alice aircraft would be the most compelling demonstration yet of the potential for battery-powered flight. But experts say there’s still a long way to go before electric aircraft makes a significant dent in the aviation industry.

The Alice is currently undergoing high-speed taxi tests at Arlington Municipal Airport close to Seattle, says Eviation CEO Omer Bar-Yohay. This involves subjecting all of the plane’s key systems and fail-safe mechanisms to a variety of different scenarios to ensure they are operating as expected before its first flight. The company is five or six good weather days away from completing those tests, says Bar-Yohay, after which the plane should be cleared for takeoff. Initial flights won’t push the aircraft to its limits, but the Alice should ultimately be capable of cruising speeds of 250 knots (463 kilometers per hour) and a maximum range of 440 nautical miles (815 kilometers).

Electric aviation has received considerable attention in recent years as the industry looks to reduce its carbon emissions. And while the Alice won’t be the first all-electric aircraft to take to the skies, Bar-Yohay says it will be the first designed with practical commercial applications in mind. Eviation plans to offer three configurations—a nine-seater commuter model, a six-seater executive model for private jet customers, and a cargo version with a capacity of 12.74 cubic meters. The company has already received advance orders from logistics giant DHL and Massachusetts-based regional airline Cape Air.

“It’s not some sort of proof-of-concept or demonstrator,” says Bar-Yohay. “It’s the first all-electric with a real-life mission, and I think that’s the big differentiator.”

Getting there has required a major engineering effort, says Bar-Yohay, because the requirements for an all-electric plane are very different from those of conventional aircraft. The biggest challenge is weight, thanks to the fact that batteries provide considerably less mileage to the pound compared to energy-dense jet fuels.

That makes slashing the weight of other components a priority and the plane features lightweight composite materials “where no composite has gone before,”’, says Bar-Yohay. The company has also done away with the bulky mechanical systems used to adjust control surfaces on the wings, and replaced them with a much lighter fly-by-wire system that uses electronic actuators controlled via electrical wires.

The company’s engineers have had to deal with a host of other complications too, from having to optimize the aerodynamics to the unique volume and weight requirements dictated by the batteries to integrating brakes designed for much heavier planes. “There is just so much optimization, so many specific things that had to be solved,” says Bar-Yohay. “In some cases, there are just no components out there that do what you need done, which weren’t built for a train, or something like that.”

Despite the huge amount of work that’s gone into it, Bar-Yohay says the Alice will be comparable in price to similar sized turboprop aircraft like the Beechcraft King Air and cheaper than small business jets like the Embraer Phenom 300. And crucially, he adds, the relative simplicity of electrical motors and actuators compared with mechanical control systems and turboprops or jets means maintenance costs will be markedly lower.

Combined with the lower cost of electricity compared to jet fuel, and even accounting for the need to replace batteries every 3,000 flight hours, Eviation expects Alice’s operating costs to be about half those of similar sized aircraft.

But there are question marks over whether the plane has an obvious market, says aviation analyst Richard Aboulafia, managing director at AeroDynamic Advisory. It’s been decades since anyone has built a regional commuter with less than 70 seats, he says, and most business jets typically require more than the 440 nautical mile range the Alice offers. Scaling up to bigger aircraft or larger ranges is also largely out of the company’s hands as it will require substantial breakthroughs in battery technology. “You need to move on to a different battery chemistry,” he says. “There isn’t even a 10-year road map to get there.”

The first commercial all-electric passenger plane is just weeks away from its maiden flight, according to its maker Israeli startup Eviation. If successful, the nine-seater Alice aircraft would be the most compelling demonstration yet of the potential for battery-powered flight. But experts say there’s still a long way to go before electric aircraft makes a significant dent in the aviation industry.

The Alice is currently undergoing high-speed taxi tests at Arlington Municipal Airport close to Seattle, says Eviation CEO Omer Bar-Yohay. This involves subjecting all of the plane’s key systems and fail-safe mechanisms to a variety of different scenarios to ensure they are operating as expected before its first flight. The company is five or six good weather days away from completing those tests, says Bar-Yohay, after which the plane should be cleared for takeoff. Initial flights won’t push the aircraft to its limits, but the Alice should ultimately be capable of cruising speeds of 250 knots (463 kilometers per hour) and a maximum range of 440 nautical miles (815 kilometers).

Electric aviation has received considerable attention in recent years as the industry looks to reduce its carbon emissions. And while the Alice won’t be the first all-electric aircraft to take to the skies, Bar-Yohay says it will be the first designed with practical commercial applications in mind. Eviation plans to offer three configurations—a nine-seater commuter model, a six-seater executive model for private jet customers, and a cargo version with a capacity of 12.74 cubic meters. The company has already received advance orders from logistics giant DHL and Massachusetts-based regional airline Cape Air.

“It’s not some sort of proof-of-concept or demonstrator,” says Bar-Yohay. “It’s the first all-electric with a real-life mission, and I think that’s the big differentiator.”

Getting there has required a major engineering effort, says Bar-Yohay, because the requirements for an all-electric plane are very different from those of conventional aircraft. The biggest challenge is weight, thanks to the fact that batteries provide considerably less mileage to the pound compared to energy-dense jet fuels.

That makes slashing the weight of other components a priority and the plane features lightweight composite materials “where no composite has gone before,”’, says Bar-Yohay. The company has also done away with the bulky mechanical systems used to adjust control surfaces on the wings, and replaced them with a much lighter fly-by-wire system that uses electronic actuators controlled via electrical wires.

The company’s engineers have had to deal with a host of other complications too, from having to optimize the aerodynamics to the unique volume and weight requirements dictated by the batteries to integrating brakes designed for much heavier planes. “There is just so much optimization, so many specific things that had to be solved,” says Bar-Yohay. “In some cases, there are just no components out there that do what you need done, which weren’t built for a train, or something like that.”

Despite the huge amount of work that’s gone into it, Bar-Yohay says the Alice will be comparable in price to similar sized turboprop aircraft like the Beechcraft King Air and cheaper than small business jets like the Embraer Phenom 300. And crucially, he adds, the relative simplicity of electrical motors and actuators compared with mechanical control systems and turboprops or jets means maintenance costs will be markedly lower.

Aircraft in the sky with white clouds below itThis is a conceptual rendering of Eviation's Alice, the first commercial all-electric passenger plane, in flight.Eviation
Combined with the lower cost of electricity compared to jet fuel, and even accounting for the need to replace batteries every 3,000 flight hours, Eviation expects Alice’s operating costs to be about half those of similar sized aircraft.

But there are question marks over whether the plane has an obvious market, says aviation analyst Richard Aboulafia, managing director at AeroDynamic Advisory. It’s been decades since anyone has built a regional commuter with less than 70 seats, he says, and most business jets typically require more than the 440 nautical mile range the Alice offers. Scaling up to bigger aircraft or larger ranges is also largely out of the company’s hands as it will require substantial breakthroughs in battery technology. “You need to move on to a different battery chemistry,” he says. “There isn’t even a 10-year road map to get there.”

An aircraft like the Alice isn’t meant to be a straight swap for today’s short-haul aircraft though, says Lynette Dray, a research fellow at University College London who studies the decarbonization of aviation. More likely it would be used for short intercity hops or for creating entirely new route networks better suited to its capabilities.

This is exactly what Bar-Yohay envisages, with the Alice’s reduced operating costs opening up new short-haul routes that were previously impractical or uneconomical. It could even make it feasible to replace larger jets with several smaller ones, he says, allowing you to provide more granular regional travel by making use of the thousands of runways around the country currently used only for recreational aviation.

The economics are far from certain though, says Dray, and if the ultimate goal is to decarbonize the aviation sector, it’s important to remember that aircraft are long-lived assets. In that respect, sustainable aviation fuels that can be used by existing aircraft are probably a more promising avenue.

Even if the Alice’s maiden flight goes well, it still faces a long path to commercialization, says Kiruba Haran, a professor of electrical and computer engineering at the University of Illinois at Urbana-Champaign. Aviation’s stringent safety requirements mean the company must show it can fly the aircraft for a long period, over and over again without incident, which has yet to be done with an all-electric plane at this scale.

Nonetheless, if the maiden flight goes according to plan it will be a major milestone for electric aviation, says Haran. “It’s exciting, right?” he says. “Anytime we do something more than, or further than, or better than, that’s always good for the industry.”

And while battery-powered electric aircraft may have little chance of disrupting the bulk of commercial aviation in the near-term, Haran says hybrid schemes that use a combination of batteries and conventional fuels (or even hydrogen) to power electric engines could have more immediate impact. The successful deployment of the Alice could go a long way to proving the capabilities of electric propulsion and building momentum behind the technology, says Haran.

“There are still a lot of skeptics out there,” he says. “This kind of flight demo will hopefully help bring those people along.”
',
 'Edd Gent',
 'IEEE Spectrum',
 'Easy', 0, NULL, '2026-03-18 10:26:00', '2026-03-18 10:26:00'),
 
(27, 'Mechanical Engineering with Transportation',
 'On the Road to Cleaner, Greener, and Faster Driving',
 'on-the-road-to-cleaner-greener-faster-driving',
 'No one likes sitting at a red light. But signalized intersections aren’t just a minor nuisance for drivers; vehicles consume fuel and emit greenhouse gases while waiting for the light to change.

What if motorists could time their trips so they arrive at the intersection when the light is green? While that might be just a lucky break for a human driver, it could be achieved more consistently by an autonomous vehicle that uses artificial intelligence to control its speed.

In a new study, MIT researchers demonstrate a machine-learning approach that can learn to control a fleet of autonomous vehicles as they approach and travel through a signalized intersection in a way that keeps traffic flowing smoothly.

Using simulations, they found that their approach reduces fuel consumption and emissions while improving average vehicle speed. The technique gets the best results if all cars on the road are autonomous, but even if only 25 percent use their control algorithm, it still leads to substantial fuel and emissions benefits.

“This is a really interesting place to intervene. No one’s life is better because they were stuck at an intersection. With a lot of other climate change interventions, there is a quality-of-life difference that is expected, so there is a barrier to entry there. Here, the barrier is much lower,” says senior author Cathy Wu, the Gilbert W. Winslow Career Development Assistant Professor in the Department of Civil and Environmental Engineering and a member of the Institute for Data, Systems, and Society (IDSS) and the Laboratory for Information and Decision Systems (LIDS).

The lead author of the study is Vindula Jayawardana, a graduate student in LIDS and the Department of Electrical Engineering and Computer Science. The research will be presented at the European Control Conference.

Intersection intricacies

While humans may drive past a green light without giving it much thought, intersections can present billions of different scenarios depending on the number of lanes, how the signals operate, the number of vehicles and their speeds, the presence of pedestrians and cyclists, etc.

Typical approaches for tackling intersection control problems use mathematical models to solve one simple, ideal intersection. That looks good on paper, but likely won’t hold up in the real world, where traffic patterns are often about as messy as they come.

Wu and Jayawardana shifted gears and approached the problem using a model-free technique known as deep reinforcement learning. Reinforcement learning is a trial-and-error method where the control algorithm learns to make a sequence of decisions. It is rewarded when it finds a good sequence. With deep reinforcement learning, the algorithm leverages assumptions learned by a neural network to find shortcuts to good sequences, even if there are billions of possibilities.

This is useful for solving a long-horizon problem like this; the control algorithm must issue upwards of 500 acceleration instructions to a vehicle over an extended time period, Wu explains.

“And we have to get the sequence right before we know that we have done a good job of mitigating emissions and getting to the intersection at a good speed,” she adds.

But there’s an additional wrinkle. The researchers want the system to learn a strategy that reduces fuel consumption and limits the impact on travel time. These goals can be conflicting.

“To reduce travel time, we want the car to go fast, but to reduce emissions, we want the car to slow down or not move at all. Those competing rewards can be very confusing to the learning agent,” Wu says.

While it is challenging to solve this problem in its full generality, the researchers employed a workaround using a technique known as reward shaping. With reward shaping, they give the system some domain knowledge it is unable to learn on its own. In this case, they penalized the system whenever the vehicle came to a complete stop, so it would learn to avoid that action.

Traffic tests

Once they developed an effective control algorithm, they evaluated it using a traffic simulation platform with a single intersection. The control algorithm is applied to a fleet of connected autonomous vehicles, which can communicate with upcoming traffic lights to receive signal phase and timing information and observe their immediate surroundings. The control algorithm tells each vehicle how to accelerate and decelerate.

Their system didn’t create any stop-and-go traffic as vehicles approached the intersection. (Stop-and-go traffic occurs when cars are forced to come to a complete stop due to stopped traffic ahead). In simulations, more cars made it through in a single green phase, which outperformed a model that simulates human drivers. When compared to other optimization methods also designed to avoid stop-and-go traffic, their technique resulted in larger fuel consumption and emissions reductions. If every vehicle on the road is autonomous, their control system can reduce fuel consumption by 18 percent and carbon dioxide emissions by 25 percent, while boosting travel speeds by 20 percent.

“A single intervention having 20 to 25 percent reduction in fuel or emissions is really incredible. But what I find interesting, and was really hoping to see, is this non-linear scaling. If we only control 25 percent of vehicles, that gives us 50 percent of the benefits in terms of fuel and emissions reduction. That means we don’t have to wait until we get to 100 percent autonomous vehicles to get benefits from this approach,” she says.

Down the road, the researchers want to study interaction effects between multiple intersections. They also plan to explore how different intersection set-ups (number of lanes, signals, timings, etc.) can influence travel time, emissions, and fuel consumption. In addition, they intend to study how their control system could impact safety when autonomous vehicles and human drivers share the road. For instance, even though autonomous vehicles may drive differently than human drivers, slower roadways and roadways with more consistent speeds could improve safety, Wu says.

While this work is still in its early stages, Wu sees this approach as one that could be more feasibly implemented in the near-term.

“The aim in this work is to move the needle in sustainable mobility. We want to dream, as well, but these systems are big monsters of inertia. Identifying points of intervention that are small changes to the system but have significant impact is something that gets me up in the morning,” she says.  

“Professor Cathy Wu's recent work shows how eco-driving provides a unified framework for reducing fuel consumption, thus minimizing carbon dioxide emissions, while also giving good results on average travel time. More specifically, the reinforcement learning approach pursued in Wu's work, by leveraging the use of connected autonomous vehicles technology, provides a feasible and attractive framework for other researchers in the same space,” says Ozan Tonguz, professor of electrical and computer engineering at Carnegie Mellon University, who was not involved with this research. “Overall, this is a very timely contribution in this burgeoning and important research area.”
',
 'Adam Zewe',
 'MIT News',
 'Intermediate', 0, NULL, '2026-03-18 10:27:00', '2026-03-18 10:27:00'),
 
(28, 'Mechanical Engineering with Transportation',
 'A New Axial-Flux Motor Becomes a Supercar Staple',
 'new-axial-flux-motor-becomes-supercar-staple',
 'Tesla was first to patent a primitive axial-flux electric motor—Nikola Tesla, that is, way back in 1889. It would be 126 years before the concept found its way to a car, the 1,500-horsepower (1,103-kilowatt), US $1.9 million, Koenigsegg Regera hybrid, in 2015. Even today, nearly all the world’s EVs and hybrids rely on relatively inefficient, easy-to-manufacture radial-flux motors.

Yet the latest electrified revolution is underway, led by YASA. Founded in the U.K. by Tim Woolmer in 2009, a spin-off from his Oxford Ph.D. project, the company’s pioneering axial-flux motors are powering hybrid supercars from a Who’s Who of makers: Ferrari, Lamborghini, McLaren, and Koenigsegg. Those include the Ferrari 296 Speciale and Lamborghini Temerario that I recently drove in Italy.

Boosted by these power-dense electric machines, these racy Italians carved up roads in Emilia-Romagna like hunks of prosciutto di Parma. The Temerario’s gasoline V-8 revs to a stratospheric 10,000 rpm, higher than any production supercar. Still not enough: The Temerario also integrates three YASA motors. A pair on the front axle deliver all-wheel-drive traction and a peak 294 hp (216 kW). A total of 907 hybrid horsepower (667 kW) sends the Temerario to a blistering 343 kilometers per hour (213 miles per hour) top speed. The electric motors ably fill any gaps in gasoline acceleration and finesse the handling with torque-vectoring, the electrified front wheels helping to catapult the Lamborghini out of corners with ridiculous ease.

With their compact design and superior power-to-weight ratio, these motors are setting records on land, sea, and air. The world’s fastest electric plane, the Rolls-Royce Spirit of Innovation, integrated three YASA motors for its propeller, sending it to a record 559.9 km/h (345.4 mph) top speed. Applying tech from its Formula E racing program, Jaguar used YASA motors to set a maritime electric speed record of 142.6 km/h (88 mph) in England’s Lake District in 2018 (that record has since been broken).

Claimed Power Density Is Three Times Tesla’s Best
In August, YASA’s motors helped the Mercedes-AMG GT XX prototype set dozens of EV endurance records. Cruising around Italy’s Nardo circuit at a sustained 186 mph (300 km/h), the roughly 1,000-kW (1,360-hp) Mercedes EV drove about 5,300 kilometers per day. In 7.5 days, it traveled 40,075 kilometers (24,902 miles), the exact equivalent of the Earth’s circumference. That time included stops for charging, at 850 kW.

Mercedes bought YASA outright in 2021. Daimler, Mercedes’s corporate parent, is retrofitting a factory in Berlin to build up to 100,000 YASA motors a year, for the next logical step: The motors will power mass-produced EVs for the first time, specifically from AMG, Mercedes’s formidable high-performance division.

The company recently unveiled its latest motor, and its stats are eye-opening: The axial-flux prototype generates a peak 750 kW, or 1,005 hp, as tested on a dynamometer. The motor can output a continuous 350-400 kW (469-536 hp). Yet the unit weighs just 12.7 kilograms (27.9 pounds). Woolmer says the resulting power density of 59 kilowatts per kilogram is an unofficial world record for an electric motor, and about three times that of leading radial-flux designs, including Tesla’s.

“And this isn’t a concept on a screen—it’s running, right now, on the dynos,” Woolmer says. “We’ve built an electric motor that’s significantly more power-dense than anything before it, all with scalable materials and processes.”

Simon Odling, YASA’s chief of new technology, walks me through the tech. Conventional, radial-flux motors are shaped like a sausage roll. A spinning rotor is housed within a stationary stator. The lines of magnetic flux are oriented radially, perpendicular to the motor’s central shaft. These flux lines represent the interacting magnetic fields of the permanent magnets in the rotor and electromagnets in the stator. It is that interaction that provides torque.

An axial flux design is more like a pancake. In YASA’s configuration, a pair of much-larger rotors are positioned on either side of the stator, and, notably, all three have roughly the same diameter. Magnetic flux is oriented axially, parallel to the shaft. Because torque is proportional to the rotor diameter squared, an axial-flux design can generate substantially more torque than a comparable radial-flux unit. The dual permanent-magnetic rotors double the key torque-generating components and ensure a short magnetic path, which enhances efficiency by reducing losses in the magnetic field.

Odling says the company’s motors are about one-third the mass and length of a comparable radial-flux machine, with intriguing upsides for vehicle packaging and weight savings. “The motor sits between an engine and gearbox really nicely in a hybrid application, or it makes for a very compact drive unit in an EV,” Odling says. The configuration is also ideal for in-wheel motors, because the flat shape fits easily within the width of car and even motorcycle wheels.

YASA also touts the weight savings. Cascading gains in vehicle architecture could eliminate at least 200 kg from today’s EVs, the company says, about half from the motors themselves, the rest from smaller batteries, brakes, and lighter-weight supporting structures.

YASA’s Secret Sauce Is a Soft Magnetic Composite
The company’s name offers another clue to its technical edge: YASA stands for “Yokeless and Segmented Architecture.” The motors remove a heavy iron or steel yoke, the structural and magnetic backbone for the copper coils in a conventional stator. Instead, they use a Soft Magnetic Composite (SMC)—a material that has very high magnetic permeability. That characteristic means the material is a very effective conductor of magnetic flux, so it can be used to concentrate and direct the field in the motor. In a typical application, the stator’s coils are wrapped around structures made of SMC.

Woolmer began studying SMCs in the mid 2000s before there were potential paying customers for his nascent motor designs: The first Tesla Roadster didn’t hit the road until 2008, and suppliers and tooling for these motors didn’t exist then. Woolmer’s early axial-flux designs finally made their way into the Jaguar C-X75 in 2010, a concept that was canceled prior to production. By 2019, Ferrari was integrating one of Woolmer’s motors in its first hybrid, the SF90.

SMC became a key innovation, because axial-flux motors couldn’t be manufactured with the stacked-steel laminations of radial-flux machines. Woolmer segmented the stator into individual “pole pieces” made from SMC, which can be formed under pressure into a huge variety of 3D shapes. That flexibility greatly reduces weight and eddy-current losses, and lessens the cooling burden. Where a conventional motor might have 30 kg of iron, a comparable YASA design would need only 5 kg to generate the same power and torque.

YASA’s stators also integrate flat copper windings with direct oil cooling, Odling says, with no “buried copper” that the oil can’t reach. That greatly improves thermal performance and recovery in stressful conditions, a potential boon for high-performance EVs.

YASA designs and develops its motors at its Oxford Innovation Center. In May, it opened a new axial-motor “super factory” in nearby Yarnton, with capacity for more than 25,000 motors each year. The company also credits the British Advanced Propulsion Center (APC) as a linchpin of its expansion. The collaboration between the U.K. government, industry, and academia looks to accelerate homegrown development of zero-emissions transportation to meet Net Zero targets.

YASA intends to release more specifics on its latest prototype motor in December. But company executives say the motor is ready for customers, with no exotic materials or manufacturing techniques required.
',
 'Lawrence Ulrich',
 'IEEE Spectrum',
 'Intermediate', 0, NULL, '2026-03-18 10:28:00', '2026-03-18 10:28:00'),
 
(29, 'Mechanical Engineering with Transportation',
 'Designing Better Batteries for Electric Vehicles',
 'designing-better-batteries-electric-vehicles',
 'The urgent need to cut carbon emissions is prompting a rapid move toward electrified mobility and expanded deployment of solar and wind on the electric grid. If those trends escalate as expected, the need for better methods of storing electrical energy will intensify.

“We need all the strategies we can get to address the threat of climate change,” says Elsa Olivetti PhD ’07, the Esther and Harold E. Edgerton Associate Professor in Materials Science and Engineering. “Obviously, developing technologies for grid-based storage at a large scale is critical. But for mobile applications — in particular, transportation — much research is focusing on adapting today’s lithium-ion battery to make versions that are safer, smaller, and can store more energy for their size and weight.”

Traditional lithium-ion batteries continue to improve, but they have limitations that persist, in part because of their structure. A lithium-ion battery consists of two electrodes — one positive and one negative — sandwiched around an organic (carbon-containing) liquid. As the battery is charged and discharged, electrically charged particles (or ions) of lithium pass from one electrode to the other through the liquid electrolyte.

One problem with that design is that at certain voltages and temperatures, the liquid electrolyte can become volatile and catch fire. “Batteries are generally safe under normal usage, but the risk is still there,” says Kevin Huang PhD ’15, a research scientist in Olivetti’s group.

Another problem is that lithium-ion batteries are not well-suited for use in vehicles. Large, heavy battery packs take up space and increase a vehicle’s overall weight, reducing fuel efficiency. But it’s proving difficult to make today’s lithium-ion batteries smaller and lighter while maintaining their energy density — that is, the amount of energy they store per gram of weight.

To solve those problems, researchers are changing key features of the lithium-ion battery to make an all-solid, or “solid-state,” version. They replace the liquid electrolyte in the middle with a thin, solid electrolyte that’s stable at a wide range of voltages and temperatures. With that solid electrolyte, they use a high-capacity positive electrode and a high-capacity, lithium metal negative electrode that’s far thinner than the usual layer of porous carbon. Those changes make it possible to shrink the overall battery considerably while maintaining its energy-storage capacity, thereby achieving a higher energy density.

“Those features — enhanced safety and greater energy density — are probably the two most-often-touted advantages of a potential solid-state battery,” says Huang. He then quickly clarifies that “all of these things are prospective, hoped-for, and not necessarily realized.” Nevertheless, the possibility has many researchers scrambling to find materials and designs that can deliver on that promise.

Thinking beyond the lab

Researchers have come up with many intriguing options that look promising — in the lab. But Olivetti and Huang believe that additional practical considerations may be important, given the urgency of the climate change challenge. “There are always metrics that we researchers use in the lab to evaluate possible materials and processes,” says Olivetti. Examples might include energy-storage capacity and charge/discharge rate. When performing basic research — which she deems both necessary and important — those metrics are appropriate. “But if the aim is implementation, we suggest adding a few metrics that specifically address the potential for rapid scaling,” she says.

Based on industry’s experience with current lithium-ion batteries, the MIT researchers and their colleague Gerbrand Ceder, the Daniel M. Tellep Distinguished Professor of Engineering at the University of California at Berkeley, suggest three broad questions that can help identify potential constraints on future scale-up as a result of materials selection. First, with this battery design, could materials availability, supply chains, or price volatility become a problem as production scales up? (Note that the environmental and other concerns raised by expanded mining are outside the scope of this study.) Second, will fabricating batteries from these materials involve difficult manufacturing steps during which parts are likely to fail? And third, do manufacturing measures needed to ensure a high-performance product based on these materials ultimately lower or raise the cost of the batteries produced?

To demonstrate their approach, Olivetti, Ceder, and Huang examined some of the electrolyte chemistries and battery structures now being investigated by researchers. To select their examples, they turned to previous work in which they and their collaborators used text- and data-mining techniques to gather information on materials and processing details reported in the literature. From that database, they selected a few frequently reported options that represent a range of possibilities.

Materials and availability

In the world of solid inorganic electrolytes, there are two main classes of materials — the oxides, which contain oxygen, and the sulfides, which contain sulfur. Olivetti, Ceder, and Huang focused on one promising electrolyte option in each class and examined key elements of concern for each of them.

The sulfide they considered was LGPS, which combines lithium, germanium, phosphorus, and sulfur. Based on availability considerations, they focused on the germanium, an element that raises concerns in part because it’s not generally mined on its own. Instead, it’s a byproduct produced during the mining of coal and zinc.

To investigate its availability, the researchers looked at how much germanium was produced annually in the past six decades during coal and zinc mining and then at how much could have been produced. The outcome suggested that 100 times more germanium could have been produced, even in recent years. Given that supply potential, the availability of germanium is not likely to constrain the scale-up of a solid-state battery based on an LGPS electrolyte.

The situation looked less promising with the researchers’ selected oxide, LLZO, which consists of lithium, lanthanum, zirconium, and oxygen. Extraction and processing of lanthanum are largely concentrated in China, and there’s limited data available, so the researchers didn’t try to analyze its availability. The other three elements are abundantly available. However, in practice, a small quantity of another element — called a dopant — must be added to make LLZO easy to process. So the team focused on tantalum, the most frequently used dopant, as the main element of concern for LLZO.

Tantalum is produced as a byproduct of tin and niobium mining. Historical data show that the amount of tantalum produced during tin and niobium mining was much closer to the potential maximum than was the case with germanium. So the availability of tantalum is more of a concern for the possible scale-up of an LLZO-based battery.

But knowing the availability of an element in the ground doesn’t address the steps required to get it to a manufacturer. So the researchers investigated a follow-on question concerning the supply chains for critical elements — mining, processing, refining, shipping, and so on. Assuming that abundant supplies are available, can the supply chains that deliver those materials expand quickly enough to meet the growing demand for batteries?

In sample analyses, they looked at how much supply chains for germanium and tantalum would need to grow year to year to provide batteries for a projected fleet of electric vehicles in 2030. As an example, an electric vehicle fleet often cited as a goal for 2030 would require production of enough batteries to deliver a total of 100 gigawatt hours of energy. To meet that goal using just LGPS batteries, the supply chain for germanium would need to grow by 50 percent from year to year — a stretch, since the maximum growth rate in the past has been about 7 percent. Using just LLZO batteries, the supply chain for tantalum would need to grow by about 30 percent — a growth rate well above the historical high of about 10 percent.

Those examples demonstrate the importance of considering both materials availability and supply chains when evaluating different solid electrolytes for their scale-up potential. “Even when the quantity of a material available isn’t a concern, as is the case with germanium, scaling all the steps in the supply chain to match the future production of electric vehicles may require a growth rate that’s literally unprecedented,” says Huang.

Materials and processing

In assessing the potential for scale-up of a battery design, another factor to consider is the difficulty of the manufacturing process and how it may impact cost. Fabricating a solid-state battery inevitably involves many steps, and a failure at any step raises the cost of each battery successfully produced. As Huang explains, “You’re not shipping those failed batteries; you’re throwing them away. But you’ve still spent money on the materials and time and processing.”

As a proxy for manufacturing difficulty, Olivetti, Ceder, and Huang explored the impact of failure rate on overall cost for selected solid-state battery designs in their database. In one example, they focused on the oxide LLZO. LLZO is extremely brittle, and at the high temperatures involved in manufacturing, a large sheet that’s thin enough to use in a high-performance solid-state battery is likely to crack or warp.

To determine the impact of such failures on cost, they modeled four key processing steps in assembling LLZO-based batteries. At each step, they calculated cost based on an assumed yield — that is, the fraction of total units that were successfully processed without failing. With the LLZO, the yield was far lower than with the other designs they examined; and, as the yield went down, the cost of each kilowatt-hour (kWh) of battery energy went up significantly. For example, when 5 percent more units failed during the final cathode heating step, cost increased by about $30/kWh — a nontrivial change considering that a commonly accepted target cost for such batteries is $100/kWh. Clearly, manufacturing difficulties can have a profound impact on the viability of a design for large-scale adoption.

Materials and performance

One of the main challenges in designing an all-solid battery comes from “interfaces” — that is, where one component meets another. During manufacturing or operation, materials at those interfaces can become unstable. “Atoms start going places that they shouldn’t, and battery performance declines,” says Huang.

As a result, much research is devoted to coming up with methods of stabilizing interfaces in different battery designs. Many of the methods proposed do increase performance; and as a result, the cost of the battery in dollars per kWh goes down. But implementing such solutions generally involves added materials and time, increasing the cost per kWh during large-scale manufacturing.

To illustrate that trade-off, the researchers first examined their oxide, LLZO. Here, the goal is to stabilize the interface between the LLZO electrolyte and the negative electrode by inserting a thin layer of tin between the two. They analyzed the impacts — both positive and negative — on cost of implementing that solution. They found that adding the tin separator increases energy-storage capacity and improves performance, which reduces the unit cost in dollars/kWh. But the cost of including the tin layer exceeds the savings so that the final cost is higher than the original cost.

In another analysis, they looked at a sulfide electrolyte called LPSCl, which consists of lithium, phosphorus, and sulfur with a bit of added chlorine. In this case, the positive electrode incorporates particles of the electrolyte material — a method of ensuring that the lithium ions can find a pathway through the electrolyte to the other electrode. However, the added electrolyte particles are not compatible with other particles in the positive electrode — another interface problem. In this case, a standard solution is to add a “binder,” another material that makes the particles stick together.

Their analysis confirmed that without the binder, performance is poor, and the cost of the LPSCl-based battery is more than $500/kWh. Adding the binder improves performance significantly, and the cost drops by almost $300/kWh. In this case, the cost of adding the binder during manufacturing is so low that essentially all the of the cost decrease from adding the binder is realized. Here, the method implemented to solve the interface problem pays off in lower costs.

The researchers performed similar studies of other promising solid-state batteries reported in the literature, and their results were consistent: The choice of battery materials and processes can affect not only near-term outcomes in the lab but also the feasibility and cost of manufacturing the proposed solid-state battery at the scale needed to meet future demand. The results also showed that considering all three factors together — availability, processing needs, and battery performance — is important because there may be collective effects and trade-offs involved.

Olivetti is proud of the range of concerns the team’s approach can probe. But she stresses that it’s not meant to replace traditional metrics used to guide materials and processing choices in the lab. “Instead, it’s meant to complement those metrics by also looking broadly at the sorts of things that could get in the way of scaling” — an important consideration given what Huang calls “the urgent ticking clock” of clean energy and climate change.

This research was supported by the Seed Fund Program of the MIT Energy Initiative (MITEI) Low-Carbon Energy Center for Energy Storage; by Shell, a founding member of MITEI; and by the U.S. Department of Energy’s Office of Energy Efficiency and Renewable Energy, Vehicle Technologies Office, under the Advanced Battery Materials Research Program. The text mining work was supported by the National Science Foundation, the Office of Naval Research, and MITEI.
',
 'Nancy W. Stauffer',
 'MIT News',
 'Advanced', 0, NULL, '2026-03-18 10:29:00', '2026-03-18 10:29:00'),
 
(30, 'Mechanical Engineering with Transportation',
 'Airbus Is Working on a Superconducting Electric Aircraft',
 'airbus-working-on-superconducting-electric-aircraft',
 'One of the greatest climate-related engineering challenges right now is the design and construction of a large, zero-emission, passenger airliner. And in this massive undertaking, no airplane maker is as invested as Airbus.

At the Airbus Summit, a symposium for journalists on 24 and 25 March, top executives sketched out a bold, tech-forward vision for the company’s next couple of generations of aircraft. The highlight of the summit—in Toulouse, France—from a tech perspective, is a superconducting, fuel-cell powered airliner.

Airbus’s strategy is based on parallel development efforts. While undertaking the enormous R&D projects needed to create the large, fuel-cell aircraft, the company said it will also work aggressively on an airliner designed to wring the most possible efficiency out of combustion-based propulsion. For this plane, the company is targeting a 20-to-30 percent reduction in fuel consumption, according to Bruno Fichefeux, head of future programs at Airbus. The plane would be a single-aisle airliner, designed to succeed Airbus’s A320 family of aircraft, the highest-selling passenger jet aircraft on the market, with nearly 12,000 delivered. The company expects the new plane to enter service some time in the latter half of the 2030s.

Airbus hopes to achieve such a large efficiency gain by exploiting emerging advances in jet engines, wings, lightweight, high-strength composite materials, and sustainable aviation fuel. For example, Airbus disclosed that it is now working on a pair of advanced jet engines, the more radical of which would have an open fan whose blades would spin without a surrounding nacelle. Airbus is evaluating such an engine in a project with partner CFM International, a joint venture between GE Aerospace and Safran Aircraft Engines.

Without a nacelle to enclose them, an engine’s fan blades can be very large, permitting higher levels of “bypass air,” which is the air sucked in to the back of the engine—separate from the air used to combust fuel—and expelled to provide thrust. The ratio of bypass air to combustion air is an important measure of engine performance, with higher ratios indicating higher efficiencies, according to Mohamed Ali, chief technology and operating officer for GE Aerospace. Typical bypass ratios today are around 11 or 12, but the open-fan design could enable ratios as high as 60, according to Ali.

The partners have already tested open-fan engines in two different series of wind-tunnel tests in Europe, Ali added. “The results have been extremely encouraging, not only because they are really good in terms of performance and noise validation, but also [because] they’re validating the computational analysis that we have done,” Ali said at the Airbus event.

Fuel-Cell Airliner Is a Cornerstone of Zero-Emission Goals
In parallel with this advanced combustion-powered airliner, Airbus has been developing a fuel-cell aircraft for five years under a program called ZEROe. At the summit, Airbus CEO Guillaume Faury backed off of a goal to fly such a plane by 2035, citing the lack of a regulatory framework for certifying such an aircraft as well as the slow pace of the build-out of infrastructure needed to produce “green” hydrogen at commercial scale and at competitive prices. “We would have the risk of a sort of ‘Concord of hydrogen’ where we would have a solution, but that would not be a commercially viable solution at scale,” Faury explained.

That said, he took pains to reaffirm the company’s commitment to the project. “We continue to believe in hydrogen,” he declared. “We’re absolutely convinced that this is an energy for the future for aviation, but there’s just more work to be done. More work for Airbus, and more work for the others around us to bring that energy to something that is at scale, that is competitive, and that will lead to a success, making a significant contribution to decarbonization.” Many of the world’s major industries, including aviation, have pledged to achieve zero net greenhouse gas emissions by the year 2050, a fact that Faury and other Airbus officials repeatedly invoked as a key driver of the ZEROe project.

Later in the event, Glenn Llewellyn, Airbus’s vice president in charge of the ZEROe program, described the project in detail, indicating an effort of breathtaking technological ambition. The envisioned aircraft would seat at least 100 people and have a range of 1,000 nautical miles (1850 kilometers). It would be powered by four fuel-cell “engines” (two on each wing), each with a power output of 2 megawatts.

According to Hauke Luedders, head of fuel cell propulsion systems development at Airbus, the company has already done extensive tests in Munich on a 1.2-MW system built with partners including Diehl, ElringKlinger, Liebherr Group, and Magna Steyr. Luedders said the company is focusing on low-temperature proton-exchange-membrane fuel cells, although it has not yet settled on the technology.

But the real stunner was Llewellyn’s description of a comprehensive program at Airbus to design and test a complete superconducting electrical power train for the fuel-cell aircraft. “As the hydrogen stored on the aircraft is stored at a very cold temperature, -253 °C, we can use this temperature and the cryogenic technology to also efficiently cool down the electrics in the full system,” Llewellyn explained. “It significantly improves the energy efficiency and the performance. And even if this is an early technology, with the right efforts and the right partnerships, this could be a game changer for our fuel-cell aircraft, for our fully electric aircraft, enabling us to design larger, more powerful, and more efficient aircraft.”

In response to a question from IEEE Spectrum, Llewellyn elaborated that all of the major components of the electric propulsion system would be cryocooled: “electric distribution system, electronic controls, power converters, and the motors”—specifically, the coils in the motors. “We’re working with partners on every single component,” he added. The cryocooling system would chill a refrigerant that would circulate to keep the components cold, he explained.

Could Aviation Be the Killer App for Superconductors?
Llewellyn did not specify which superconductors and refrigerants the team was working with. But high-temperature superconductors are a good bet because of the drastically reduced requirements on the cooling system that would be needed to sustain superconductivity.

Copper-oxide based ceramic superconductors were invented at IBM in 1986, and various forms of them can superconduct at temperatures between -238 °C (35 kelvins) and -140 °C (133 K) at ambient pressure. These temperatures are higher than traditional superconductors, which need temperatures below about 25 K. Nevertheless, commercial applications for the high-temperature superconductors have been elusive.

But a superconductivity expert, applied physicist Yu He at Yale University, was heartened by the news from Airbus. “My first reaction was, Really? And my second reaction was, Wow, this whole line of research, or application, is indeed growing and I’m very delighted” about Airbus’s ambitious plans.

Copper oxide superconductors have been used in a few applications, almost all of them experimental. These included wind-turbine generators, magnetic-levitation train demonstrations, short electrical-transmission cables, magnetic-resonance imaging machines and, notably, in the electromagnet coils for experimental tokamak fusion reactors.

The tokamak application, at a fusion startup called Commonwealth Fusion Systems, is particularly relevant because to make coils, engineers had to invent a process for turning the normally brittle copper oxide superconducting material into a tape that could be used to form doughnut-shaped coils capable of sustaining very high current flow and therefore very intense magnetic fields.

“Having a superconductor to provide such a large current is desirable because it doesn’t generate heat,” says He. “That means, first, you have much less energy lost directly from the coils themselves. And, second, you don’t require as much cooling power to remove the heat.”

Still, the technical hurdles are substantial. “One can argue that inside the motor, intense heat will still need to be removed due to aerodynamic friction,” He says. “Then it becomes, how do you manage the overall heat within the motor?”

For this challenge, engineers will at least have a favorable environment with cold, fast-flowing air. Engineers will be able to tap into the “massive air flow” over the motors and other components to assist the cooling, He suggests. Smart design could “take advantage of this kinetic energy of flowing air.”

To test the evolving fuel-cell propulsion system, Airbus has built a unique test center in Grenoble, France, called the Liquid Hydrogen Breadboard, Llewellyn disclosed at the summit. “We partnered with Air Liquide Advanced Technologies” to build the facility, he said. “This Breadboard is a versatile test platform designed to simulate key elements of future aircraft architecture: tanks, valves, pipes, and pumps, allowing us to validate different configurations at full scale. And this test facility is helping us gain critical insight into safety, hydrogen operations, tank design, refueling, venting, and gauging.”

“Throughout 2025, we’re going to continue testing the complete liquid-hydrogen and distribution system,” Llewellyn added. “And by 2027, our objective is to take an even further major step forward, testing the complete end-to-end system, including the fuel-cell engine and the liquid hydrogen storage and distribution system together, which will allow us to assess the full system in action.”
',
 'Glenn Zorpette',
 'IEEE Spectrum',
 'Advanced', 0, NULL, '2026-03-18 10:30:00', '2026-03-18 10:30:00');
--
-- Dumping data for table `articles`
--

LOCK TABLES `articles` WRITE;
/*!40000 ALTER TABLE `articles` DISABLE KEYS */;
/*!40000 ALTER TABLE `articles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favorites` (
  `user_id` int NOT NULL,
  `article_id` int NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`article_id`),
  UNIQUE KEY `unique_user_favorite` (`user_id`,`article_id`),
  KEY `article_id` (`article_id`),
  CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `articles` (`article_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `favorites`
--

LOCK TABLES `favorites` WRITE;
/*!40000 ALTER TABLE `favorites` DISABLE KEYS */;
/*!40000 ALTER TABLE `favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question_attempts`
--

DROP TABLE IF EXISTS `question_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `question_attempts` (
  `user_id` int NOT NULL,
  `question_id` int NOT NULL,
  `user_answer` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`question_id`),
  KEY `user_id` (`user_id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `question_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `question_attempts_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question_attempts`
--

LOCK TABLES `question_attempts` WRITE;
/*!40000 ALTER TABLE `question_attempts` DISABLE KEYS */;
/*!40000 ALTER TABLE `question_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `questions` (
  `question_id` int NOT NULL AUTO_INCREMENT,
  `article_id` int NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` json NOT NULL COMMENT '‘''JSON format：{"A":"option content ","B":"option content"...}''',
  `answer` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `explanation` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`question_id`),
  KEY `article_id` (`article_id`),
  CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`article_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- ----------------------------------------
-- Article 1: Carbon-Negative Concrete (CE, Easy)
-- ----------------------------------------
INSERT INTO questions (question_id, article_id, content, options, answer, explanation, deleted_at, created_at, updated_at) VALUES
(1, 1,
 'What does "carbon-negative" most likely mean in the context of this article?',
 '{"A":"The material releases a large amount of carbon dioxide","B":"The material absorbs more carbon dioxide than it produces during manufacturing","C":"The material contains no carbon atoms","D":"The material is painted black to absorb heat"}',
 'B',
 'A carbon-negative material removes more CO₂ from the environment than is emitted during its production process.',
 NULL, '2026-03-18 10:40:00', '2026-03-18 10:40:00'),
 
(2, 1,
 'What can be inferred about why the researchers chose seawater as a key ingredient?',
 '{"A":"Seawater is the cheapest liquid available","B":"Seawater contains dissolved minerals that can be solidified through electrochemical processes to form building materials","C":"Seawater is easier to transport than freshwater","D":"Seawater has a pleasant color for construction"}',
 'B',
 'The article describes how applying electrical current to seawater precipitates solid minerals that can replace sand in concrete, leveraging seawater''s mineral content.',
 NULL, '2026-03-18 10:40:01', '2026-03-18 10:40:01'),
 
(3, 1,
 'What is the main achievement described in this article?',
 '{"A":"Discovering a new type of seashell","B":"Developing a building material that captures CO₂ during production and can partially replace traditional concrete ingredients","C":"Finding a way to make seawater drinkable","D":"Inventing a new method of underwater construction"}',
 'B',
 'The article reports on a new carbon-negative material created by injecting CO₂ into seawater, producing minerals that can substitute for sand in concrete.',
 NULL, '2026-03-18 10:40:02', '2026-03-18 10:40:02'),
 
-- ----------------------------------------
-- Article 2: Bacteria Self-Healing Concrete (CE, Easy)
-- ----------------------------------------
(4, 2,
 'What does the term "self-healing" mean when applied to concrete in this article?',
 '{"A":"The concrete can cure diseases","B":"The concrete can automatically repair its own cracks without human intervention","C":"The concrete heals faster than other materials when heated","D":"The concrete is used in hospital construction"}',
 'B',
 'Self-healing concrete contains biological or chemical agents that activate when cracks form, filling and sealing the damage autonomously.',
 NULL, '2026-03-18 10:40:03', '2026-03-18 10:40:03'),
 
(5, 2,
 'Based on the article, why did the researchers embed bacteria inside polymer fibers rather than mixing them directly into concrete?',
 '{"A":"Because bacteria cannot survive in any form of concrete","B":"Because the polymer fibers protect the bacteria and allow them to remain dormant until cracks expose them to water and air","C":"Because polymer fibers are cheaper than bacteria","D":"Because direct mixing would change the color of the concrete"}',
 'B',
 'The BioFiber design encases bacteria in protective hydrogel within polymer fibers, keeping them viable until crack formation triggers their activation.',
 NULL, '2026-03-18 10:40:04', '2026-03-18 10:40:04'),
 
(6, 2,
 'What is the primary innovation described in this article?',
 '{"A":"A new type of paint for concrete surfaces","B":"Polymer fibers containing bacteria-laden hydrogel that can heal cracks in concrete structures","C":"A method to make concrete lighter","D":"A technique for recycling old concrete into new buildings"}',
 'B',
 'The article introduces BioFiber technology — polymer fibers with embedded bacteria that activate to seal cracks when damage occurs.',
 NULL, '2026-03-18 10:40:05', '2026-03-18 10:40:05'),
 
-- ----------------------------------------
-- Article 3: Cement vs Concrete (CE, Intermediate)
-- ----------------------------------------
(7, 3,
 'What does "hydration" refer to in the context of cement chemistry?',
 '{"A":"Adding water to plants near a construction site","B":"The chemical reaction between cement powder and water that causes the mixture to harden","C":"The process of keeping concrete wet after it is poured","D":"Drinking water during construction work"}',
 'B',
 'Hydration is the exothermic chemical reaction where water reacts with cement compounds to form a hardite paste that binds aggregates together.',
 NULL, '2026-03-18 10:40:06', '2026-03-18 10:40:06'),
 
(8, 3,
 'What can be inferred about why the article emphasizes the distinction between cement and concrete?',
 '{"A":"Because they are exactly the same material","B":"Because confusing the two leads to misunderstanding about where sustainability interventions should be targeted","C":"Because concrete is always more expensive than cement","D":"Because cement is no longer used in modern construction"}',
 'B',
 'Understanding that cement is just one component of concrete helps identify where CO₂ emissions originate and where reduction strategies should focus.',
 NULL, '2026-03-18 10:40:07', '2026-03-18 10:40:07'),
 
(9, 3,
 'What is the main purpose of this article?',
 '{"A":"To advertise a new brand of cement","B":"To clarify the differences between cement and concrete and explain opportunities for making both more sustainable","C":"To argue that concrete should be banned","D":"To compare construction practices in different countries"}',
 'B',
 'The article systematically explains the cement-concrete distinction and discusses sustainability strategies including blended cements and carbon capture.',
 NULL, '2026-03-18 10:40:08', '2026-03-18 10:40:08'),
 
-- ----------------------------------------
-- Article 4: Concrete Emissions (CE, Intermediate)
-- ----------------------------------------
(10, 4,
 'What does "life-cycle assessment" mean as used in this article?',
 '{"A":"A method to predict how long a building will last","B":"A comprehensive analysis of all environmental impacts associated with a product from raw material extraction through disposal","C":"A test that measures the strength of concrete over time","D":"An insurance evaluation for construction projects"}',
 'B',
 'Life-cycle assessment (LCA) evaluates the total environmental footprint of a material or product across its entire lifespan.',
 NULL, '2026-03-18 10:40:09', '2026-03-18 10:40:09'),
 
(11, 4,
 'According to the article, what is the relationship between operational emissions and embodied emissions in buildings?',
 '{"A":"They are the same thing","B":"Operational emissions from energy use currently dominate, but as grids decarbonize, embodied emissions from materials become proportionally more significant","C":"Embodied emissions are always higher than operational emissions","D":"Neither type of emission is significant for buildings"}',
 'B',
 'The article explains that as electricity grids become cleaner, the relative importance of embodied emissions in materials like concrete increases.',
 NULL, '2026-03-18 10:40:10', '2026-03-18 10:40:10'),
 
(12, 4,
 'What is the central finding of the MIT study described in this article?',
 '{"A":"Concrete construction should be stopped entirely","B":"U.S. building and pavement emissions could be reduced by 57–65% by 2050 through specific reduction strategies","C":"Only electric vehicles can reduce transportation emissions","D":"Building emissions have no connection to concrete production"}',
 'B',
 'The MIT study projects substantial emission reductions through combined strategies addressing both operational energy and embodied emissions in concrete.',
 NULL, '2026-03-18 10:40:11', '2026-03-18 10:40:11'),
 
-- ----------------------------------------
-- Article 5: Earthquake-Resistant Building (CE, Advanced)
-- ----------------------------------------
(13, 5,
 'What does "base isolation" refer to in earthquake engineering?',
 '{"A":"Isolating the building''s foundation from public access","B":"A technique that decouples a structure from ground motion by placing flexible bearings between the foundation and the building","C":"Building the foundation deeper underground","D":"Removing the base of a building during an earthquake"}',
 'B',
 'Base isolation involves inserting flexible pads or bearings between a building and its foundation so that seismic ground motion is not directly transmitted to the structure.',
 NULL, '2026-03-18 10:40:12', '2026-03-18 10:40:12'),
 
(14, 5,
 'Based on the article, what can be inferred about the role of shape-memory alloys in seismic design?',
 '{"A":"They are used solely for aesthetic purposes","B":"They can deform during an earthquake and return to their original shape afterward, reducing permanent structural damage","C":"They prevent all earthquakes from occurring","D":"They are only used in bridges, not buildings"}',
 'B',
 'Shape-memory alloys can undergo significant deformation and then recover their original form, allowing structures to absorb seismic energy without permanent damage.',
 NULL, '2026-03-18 10:40:13', '2026-03-18 10:40:13'),
 
(15, 5,
 'What is the primary scope of this article?',
 '{"A":"The history of famous earthquakes worldwide","B":"A comprehensive overview of technologies and methods used to make buildings resistant to earthquake damage","C":"A comparison of building codes in different countries","D":"Instructions for evacuating buildings during earthquakes"}',
 'B',
 'The article covers a broad range of earthquake-resistant technologies including base isolation, dampers, shear walls, new materials, and computational methods.',
 NULL, '2026-03-18 10:40:14', '2026-03-18 10:40:14'),
 
-- ----------------------------------------
-- Article 6: California Bridge (CE, Advanced)
-- ----------------------------------------
(16, 6,
 'What is a "cofferdam" as described in this article?',
 '{"A":"A dam built to store coffee beans","B":"A temporary watertight enclosure built within a body of water to allow construction work on foundations in dry conditions","C":"A permanent dam used for flood control","D":"A type of bridge support structure"}',
 'B',
 'A cofferdam is a temporary structure that holds back water so that an enclosed area can be pumped dry for construction of foundations or other underwater structures.',
 NULL, '2026-03-18 10:40:15', '2026-03-18 10:40:15'),
 
(17, 6,
 'What can be inferred about why the Auburn Dam project was ultimately never completed?',
 '{"A":"The construction company went bankrupt","B":"Discovery of seismic risks in the area and evolving safety standards made the original dam design unacceptable","C":"The river dried up completely","D":"Local residents preferred a bridge instead"}',
 'B',
 'The article describes how reservoir-induced seismicity concerns and changing safety evaluations led to the abandonment of the dam project.',
 NULL, '2026-03-18 10:40:16', '2026-03-18 10:40:16'),
 
(18, 6,
 'What is the main narrative of this article?',
 '{"A":"How to design modern suspension bridges","B":"The engineering history of Foresthill Bridge and the failed Auburn Dam project, exploring how geotechnical and seismic challenges shaped infrastructure decisions","C":"A tourist guide to California bridges","D":"A comparison of bridge construction methods worldwide"}',
 'B',
 'The article weaves together the stories of the Foresthill Bridge and Auburn Dam, using them to explore complex engineering challenges including seismicity and geotechnical investigation.',
 NULL, '2026-03-18 10:40:17', '2026-03-18 10:40:17'),
 
-- ----------------------------------------
-- Article 7: Algebra Oldest Problem (Math, Easy)
-- ----------------------------------------
(19, 7,
 'What are "polynomials" as explained in this article?',
 '{"A":"A type of geometric shape","B":"Equations involving a variable raised to various powers, such as x² + 3x + 2 = 0","C":"A branch of statistics","D":"A method for counting prime numbers"}',
 'B',
 'The article defines polynomials as equations where a variable is raised to different powers, providing familiar examples to illustrate the concept.',
 NULL, '2026-03-18 10:40:18', '2026-03-18 10:40:18'),
 
(20, 7,
 'What can be inferred about why solving higher-degree polynomial equations has been considered impossible since 1832?',
 '{"A":"Because mathematicians lost interest in algebra","B":"Because Galois proved that no general formula using radicals exists for equations of degree five or higher","C":"Because computers had not been invented yet","D":"Because higher-degree polynomials do not exist"}',
 'B',
 'The article references Galois''s impossibility proof, which established that the traditional approach of finding root formulas using radicals cannot work beyond degree four.',
 NULL, '2026-03-18 10:40:19', '2026-03-18 10:40:19'),
 
(21, 7,
 'What is the main news reported in this article?',
 '{"A":"A new calculator was invented","B":"A mathematician found a novel way to solve higher-order polynomial equations using new number sequences called the Geode","C":"Ancient Babylonian mathematics was proven wrong","D":"A computer program can now factor all polynomials instantly"}',
 'B',
 'The article reports on Norman Wildberger''s breakthrough method for solving polynomial equations using newly discovered number sequences.',
 NULL, '2026-03-18 10:40:20', '2026-03-18 10:40:20'),
 
-- ----------------------------------------
-- Article 8: James Maynard (Math, Easy)
-- ----------------------------------------
(22, 8,
 'What is the "twin prime conjecture" as described in this article?',
 '{"A":"A theory that all prime numbers are even","B":"The unproven hypothesis that there are infinitely many pairs of prime numbers that differ by exactly 2","C":"A method for finding the largest known prime number","D":"A rule that prime numbers always appear in groups of three"}',
 'B',
 'The twin prime conjecture states that there are infinitely many prime pairs like (11, 13) or (17, 19) where the gap is exactly 2.',
 NULL, '2026-03-18 10:40:21', '2026-03-18 10:40:21'),
 
(23, 8,
 'What can be inferred about the significance of reducing the proven prime gap from 70 million to 600?',
 '{"A":"It means the twin prime conjecture is now completely solved","B":"It represents enormous progress toward proving the twin prime conjecture, though the final gap of 2 has not yet been proven","C":"The number 600 has special mathematical properties","D":"It proves that there are exactly 600 twin prime pairs"}',
 'B',
 'Reducing the gap from 70 million to 600 shows dramatic progress, but the ultimate goal of proving a gap of 2 (twin primes) remains unachieved.',
 NULL, '2026-03-18 10:40:22', '2026-03-18 10:40:22'),
 
(24, 8,
 'What is this article primarily about?',
 '{"A":"The history of the Fields Medal award ceremony","B":"The mathematical achievements of James Maynard, particularly his work on gaps between prime numbers","C":"How to calculate prime numbers by hand","D":"A comparison of different mathematicians'' salaries"}',
 'B',
 'The article introduces Fields Medal winner James Maynard and explains his breakthroughs in understanding the distribution and gaps of prime numbers.',
 NULL, '2026-03-18 10:40:23', '2026-03-18 10:40:23'),
 
-- ----------------------------------------
-- Article 9: Bell Curves (Math, Intermediate)
-- ----------------------------------------
(25, 9,
 'What does "central limit theorem" refer to in this article?',
 '{"A":"A theorem about the geographic center of a data set","B":"A fundamental result in probability stating that the sum of many independent random variables tends toward a normal (bell curve) distribution","C":"A rule about centering text in mathematical papers","D":"A theory that the middle value of any data set is always the most important"}',
 'B',
 'The central limit theorem explains why averages and sums of many independent random variables approximate a normal distribution regardless of the original distribution.',
 NULL, '2026-03-18 10:40:24', '2026-03-18 10:40:24'),
 
(26, 9,
 'Based on the article, why do bell curves appear in so many different natural phenomena?',
 '{"A":"Because nature prefers symmetrical shapes","B":"Because many real-world measurements result from the sum of numerous small independent factors, which the central limit theorem predicts will produce a normal distribution","C":"Because scientists only measure things that follow bell curves","D":"Because all measurement instruments produce bell-shaped errors"}',
 'B',
 'The article explains that the ubiquity of bell curves arises because many natural quantities are the aggregate of many small, independent contributions.',
 NULL, '2026-03-18 10:40:25', '2026-03-18 10:40:25'),
 
(27, 9,
 'What is the main idea of this article?',
 '{"A":"How to draw a perfect bell curve","B":"Explaining the mathematical reason — the central limit theorem — behind the surprising prevalence of bell-shaped distributions across science and everyday life","C":"A biography of Carl Friedrich Gauss","D":"Why statistics courses are required in college"}',
 'B',
 'The article traces the history and logic of the central limit theorem to explain why normal distributions are so common in nature.',
 NULL, '2026-03-18 10:40:26', '2026-03-18 10:40:26'),
 
-- ----------------------------------------
-- Article 10: Game Theory Algorithms (Math, Intermediate)
-- ----------------------------------------
(28, 10,
 'What is "Nash equilibrium" as explained in this article?',
 '{"A":"A state where one player always wins","B":"A situation in a game where no player can improve their outcome by unilaterally changing their strategy","C":"The point where a game ends in a tie","D":"A mathematical formula invented by John Nash for pricing products"}',
 'B',
 'Nash equilibrium is a stable state where each player''s strategy is optimal given the strategies of all other players, so no one has an incentive to deviate.',
 NULL, '2026-03-18 10:40:27', '2026-03-18 10:40:27'),
 
(29, 10,
 'What can be inferred about the ethical implications of algorithmic pricing discussed in the article?',
 '{"A":"Algorithms always lower prices for consumers","B":"Pricing algorithms may produce collusive outcomes that harm consumers even without explicit coordination between companies","C":"Only human-set prices can be unfair","D":"Government regulation of algorithms is unnecessary"}',
 'B',
 'The article explores how autonomous pricing algorithms can converge on higher prices through game-theoretic dynamics, effectively harming consumers without explicit human collusion.',
 NULL, '2026-03-18 10:40:28', '2026-03-18 10:40:28'),
 
(30, 10,
 'What is the central topic of this article?',
 '{"A":"How to win at rock-paper-scissors","B":"How game theory explains the way pricing algorithms can inadvertently drive prices higher without explicit collusion","C":"A history of online shopping","D":"Why some products are more expensive than others"}',
 'B',
 'The article uses game theory frameworks to analyze how independent pricing algorithms can produce supra-competitive prices.',
 NULL, '2026-03-18 10:40:29', '2026-03-18 10:40:29'),
 
-- ----------------------------------------
-- Article 11: Tracy-Widom Distribution (Math, Advanced)
-- ----------------------------------------
(31, 11,
 'What does "universality" mean in the mathematical context of this article?',
 '{"A":"A theory that applies only to the universe","B":"The phenomenon where the same statistical distribution appears across vastly different systems regardless of their specific details","C":"A teaching method used in all universities","D":"A rule that all mathematical proofs must be universal"}',
 'B',
 'In random matrix theory, universality refers to the remarkable appearance of the same distribution in diverse systems — from bus schedules to nuclear physics.',
 NULL, '2026-03-18 10:40:30', '2026-03-18 10:40:30'),
 
(32, 11,
 'What can be inferred about why the Tracy-Widom distribution is considered surprising to mathematicians?',
 '{"A":"Because it only appears in one specific type of experiment","B":"Because it emerges in wildly unrelated systems — from nuclear energy levels to bacterial colony growth — suggesting deep underlying mathematical connections","C":"Because it contradicts the central limit theorem entirely","D":"Because it was discovered by accident during a cooking experiment"}',
 'B',
 'The article emphasizes that the Tracy-Widom distribution''s appearance across unrelated physical and mathematical systems hints at profound, not yet fully understood, universal principles.',
 NULL, '2026-03-18 10:40:31', '2026-03-18 10:40:31'),
 
(33, 11,
 'What is the main subject of this article?',
 '{"A":"A new method for predicting earthquakes","B":"The Tracy-Widom distribution, a statistical law that appears across diverse systems involving correlated random variables","C":"How to calculate eigenvalues by hand","D":"The biography of two statisticians named Tracy and Widom"}',
 'B',
 'The article explores the Tracy-Widom distribution as a new universal law governing the behavior of correlated systems across physics and mathematics.',
 NULL, '2026-03-18 10:40:32', '2026-03-18 10:40:32'),
 
-- ----------------------------------------
-- Article 12: Serfaty Interview (Math, Advanced)
-- ----------------------------------------
(34, 12,
 'In the context of this interview, what are "Ginzburg-Landau equations"?',
 '{"A":"Equations used to calculate land prices in Germany","B":"Mathematical equations that describe the behavior of superconductors, particularly the formation and dynamics of magnetic vortices","C":"A system of equations for predicting weather patterns","D":"Formulas used in financial accounting"}',
 'B',
 'The Ginzburg-Landau equations are a mathematical model used in condensed matter physics to describe superconductivity and the behavior of vortices in superconducting materials.',
 NULL, '2026-03-18 10:40:33', '2026-03-18 10:40:33'),
 
(35, 12,
 'What can be inferred about Serfaty''s view on the nature of mathematical truth?',
 '{"A":"She believes mathematics is purely subjective","B":"She sees mathematical truth as absolute and verifiable — unlike other domains, mathematics provides certainty that cannot be faked or disputed","C":"She thinks mathematical proofs are merely opinions","D":"She believes truth in mathematics changes over time"}',
 'B',
 'The title and interview content convey Serfaty''s conviction that mathematics offers a unique form of truth — rigorous, verifiable, and immune to deception.',
 NULL, '2026-03-18 10:40:34', '2026-03-18 10:40:34'),
 
(36, 12,
 'What is the primary focus of this article?',
 '{"A":"A review of a mathematics textbook","B":"An in-depth interview with mathematician Sylvia Serfaty about her research on superconductor equations, her problem-solving process, and her philosophy of mathematical truth","C":"A news report about a mathematics competition","D":"Instructions for solving differential equations"}',
 'B',
 'The article is a long-form interview exploring Serfaty''s research, her 18-year journey to solve a key problem, and her reflections on mathematics as a pursuit of truth.',
 NULL, '2026-03-18 10:40:35', '2026-03-18 10:40:35'),
 
-- ----------------------------------------
-- Article 13: Generative AI Explained (CS, Easy)
-- ----------------------------------------
(37, 13,
 'What does "generative AI" mean as described in this article?',
 '{"A":"AI that generates electricity","B":"AI systems that can create new content — such as text, images, or music — rather than just analyzing existing data","C":"AI that only works with numbers","D":"A type of computer hardware"}',
 'B',
 'Generative AI refers to models that learn patterns from training data and can produce novel outputs that resemble that data.',
 NULL, '2026-03-18 10:40:36', '2026-03-18 10:40:36'),
 
(38, 13,
 'Based on the article, how does generative AI differ from traditional machine learning?',
 '{"A":"Generative AI is older than traditional machine learning","B":"Traditional ML typically classifies or predicts based on input data, while generative AI creates entirely new data that resembles its training examples","C":"There is no difference between them","D":"Traditional ML can only work with text, while generative AI only works with images"}',
 'B',
 'The article distinguishes generative models (which produce new content) from discriminative models (which classify or label existing data).',
 NULL, '2026-03-18 10:40:37', '2026-03-18 10:40:37'),
 
(39, 13,
 'What is the main purpose of this article?',
 '{"A":"To criticize generative AI technology","B":"To explain what generative AI is, how it works, and why it has become significant","C":"To provide a step-by-step coding tutorial","D":"To compare different AI companies"}',
 'B',
 'The article provides an accessible introduction to generative AI covering key concepts, technologies, and applications.',
 NULL, '2026-03-18 10:40:38', '2026-03-18 10:40:38'),
 
-- ----------------------------------------
-- Article 14: Robot Helping Humans (CS, Easy)
-- ----------------------------------------
(40, 14,
 'What does the word "perception" mean in the context of robotics in this article?',
 '{"A":"A robot''s ability to feel emotions","B":"A robot''s ability to sense, interpret, and understand its surrounding environment using sensors and algorithms","C":"The speed at which a robot moves","D":"The cost of building a robot"}',
 'B',
 'In robotics, perception refers to the systems and algorithms that enable a robot to sense and make sense of its environment.',
 NULL, '2026-03-18 10:40:39', '2026-03-18 10:40:39'),
 
(41, 14,
 'Why is the concept of "relevance" important for the robot system described in the article?',
 '{"A":"Because robots need to understand human social etiquette","B":"Because filtering out irrelevant objects allows the robot to focus computational resources on items that matter most for the task at hand","C":"Because relevant objects are always the most expensive","D":"Because the robot can only see one object at a time"}',
 'B',
 'The Relevance system helps robots prioritize which objects to attend to, improving efficiency in cluttered real-world environments.',
 NULL, '2026-03-18 10:40:40', '2026-03-18 10:40:40'),
 
(42, 14,
 'What is this article primarily about?',
 '{"A":"How robots are replacing humans in offices","B":"An MIT-developed system that helps robots identify the most relevant objects in a scene to better assist humans","C":"A new type of camera for smartphones","D":"How to build a robot at home"}',
 'B',
 'The article describes the Relevance framework that enables robots to focus on the most task-relevant objects when assisting people.',
 NULL, '2026-03-18 10:40:41', '2026-03-18 10:40:41'),
 
-- ----------------------------------------
-- Article 15: AI Environmental Impact (CS, Intermediate)
-- ----------------------------------------
(43, 15,
 'What does the term "inference" refer to in the context of AI energy consumption?',
 '{"A":"Drawing a logical conclusion from evidence","B":"The process of running a trained AI model to generate outputs in response to user queries, which consumes significant energy at scale","C":"The initial phase of building an AI model","D":"A type of computer memory"}',
 'B',
 'In AI systems, inference is the operational phase where the trained model processes inputs and generates outputs, distinct from the training phase.',
 NULL, '2026-03-18 10:40:42', '2026-03-18 10:40:42'),
 
(44, 15,
 'What can be inferred about the long-term environmental challenge of generative AI?',
 '{"A":"AI''s environmental impact will decrease automatically over time","B":"As AI adoption grows, the cumulative energy and water demands of data centers could become a significant environmental burden unless efficiency improvements keep pace","C":"Only training AI models consumes energy; inference is free","D":"Moving data centers to colder climates solves all environmental problems"}',
 'B',
 'The article presents data showing rapidly growing energy demands and suggests that without proactive measures, AI infrastructure will have mounting environmental costs.',
 NULL, '2026-03-18 10:40:43', '2026-03-18 10:40:43'),
 
(45, 15,
 'What is the central theme of this article?',
 '{"A":"How to make AI models more accurate","B":"Examining the environmental costs of generative AI including electricity consumption, water usage, and hardware manufacturing impacts","C":"A comparison of different AI chatbots","D":"How data centers are designed architecturally"}',
 'B',
 'The article comprehensively examines the environmental footprint of generative AI across energy, water, and materials dimensions.',
 NULL, '2026-03-18 10:40:44', '2026-03-18 10:40:44'),
 
-- ----------------------------------------
-- Article 16: Robot Perception (CS, Intermediate)
-- ----------------------------------------
(46, 16,
 'What does "SLAM" stand for and mean in this article?',
 '{"A":"Simultaneous Learning And Memorization — a study technique","B":"Simultaneous Localization And Mapping — a technique enabling robots to build a map of their environment while tracking their own position within it","C":"Systematic Language Analysis Model — an NLP technique","D":"Standard Laser Alignment Method — a calibration tool"}',
 'B',
 'SLAM is a fundamental robotics capability that allows a robot to construct a map of an unknown environment while simultaneously determining its location within that map.',
 NULL, '2026-03-18 10:40:45', '2026-03-18 10:40:45'),
 
(47, 16,
 'Based on the article, what can be inferred about the connection between computer vision and language models in modern robotics?',
 '{"A":"They are completely separate fields that never interact","B":"Combining visual perception with language understanding enables robots to interpret complex environments more like humans do","C":"Language models have replaced computer vision entirely","D":"Computer vision is only used for taking photographs"}',
 'B',
 'The article discusses how integrating spatial AI, deep learning vision, and language models creates richer environmental understanding for robots.',
 NULL, '2026-03-18 10:40:46', '2026-03-18 10:40:46'),
 
(48, 16,
 'What is the main focus of this article?',
 '{"A":"How to program a chatbot","B":"The work of MIT professor Luca Carlone in advancing robot perception through SLAM, computer vision, spatial AI, and deep learning","C":"A review of consumer robot vacuum cleaners","D":"The history of artificial intelligence"}',
 'B',
 'The article profiles Carlone''s research on giving robots human-like environmental awareness through advanced perception technologies.',
 NULL, '2026-03-18 10:40:47', '2026-03-18 10:40:47'),
 
-- ----------------------------------------
-- Article 17: Quantum vs Classical (CS, Advanced)
-- ----------------------------------------
(49, 17,
 'What does "quantum supremacy" mean as discussed in this article?',
 '{"A":"The political dominance of countries with quantum computers","B":"The point at which a quantum computer can solve a problem that no classical computer can solve in a reasonable amount of time","C":"The highest quality level of a quantum computer","D":"A quantum computer that is larger than a classical computer"}',
 'B',
 'Quantum supremacy (or quantum advantage) refers to the milestone where a quantum device outperforms all classical computers on a specific computational task.',
 NULL, '2026-03-18 10:40:48', '2026-03-18 10:40:48'),
 
(50, 17,
 'What can be inferred about the "de-quantization" trend described in the article?',
 '{"A":"It means quantum computers are becoming less popular","B":"Classical algorithm researchers are finding ways to match some quantum speedups, narrowing the proven advantage of quantum computing","C":"Quantum computers are being converted back into classical computers","D":"It refers to reducing the size of quantum computers"}',
 'B',
 'The article describes how classical algorithmists have been developing techniques that replicate some quantum advantages, challenging assumptions about quantum superiority.',
 NULL, '2026-03-18 10:40:49', '2026-03-18 10:40:49'),
 
(51, 17,
 'What is the main argument of this article?',
 '{"A":"Quantum computers have already replaced classical computers","B":"The competition between quantum and classical computing is more nuanced than expected, with classical algorithms repeatedly closing gaps that quantum methods were thought to uniquely exploit","C":"Classical computers are obsolete","D":"Quantum computing research should be stopped"}',
 'B',
 'The article presents the ongoing rivalry between quantum and classical algorithms, showing that classical methods have repeatedly challenged presumed quantum advantages.',
 NULL, '2026-03-18 10:40:50', '2026-03-18 10:40:50'),
 
-- ----------------------------------------
-- Article 18: Graph Traversal (CS, Advanced)
-- ----------------------------------------
(52, 18,
 'What does "universally optimal" mean in the context of Dijkstra''s algorithm as described in this article?',
 '{"A":"The algorithm works on every computer operating system","B":"The algorithm is the best possible approach for every possible network layout, not just for worst-case scenarios","C":"The algorithm was accepted by every university","D":"The algorithm can solve any mathematical problem"}',
 'B',
 'Universal optimality means the algorithm performs at least as well as any other algorithm on every individual input, a stronger guarantee than worst-case optimality.',
 NULL, '2026-03-18 10:40:51', '2026-03-18 10:40:51'),
 
(53, 18,
 'What can be inferred about the difference between "worst-case optimality" and "universal optimality"?',
 '{"A":"They mean exactly the same thing","B":"Worst-case optimality guarantees good performance on the hardest inputs, while universal optimality guarantees the best performance across all possible inputs","C":"Universal optimality is easier to prove","D":"Worst-case optimality is always more useful in practice"}',
 'B',
 'The article explains that universal optimality is a stronger claim — it means no algorithm can beat it on any input, not just the hardest ones.',
 NULL, '2026-03-18 10:40:52', '2026-03-18 10:40:52'),
 
(54, 18,
 'What is the main finding reported in this article?',
 '{"A":"A new algorithm was invented to replace Dijkstra''s","B":"Researchers proved that Dijkstra''s nearly 70-year-old shortest-path algorithm is universally optimal — the best approach for every possible network","C":"Graph algorithms are no longer needed in computer science","D":"Dijkstra''s algorithm was found to have a critical bug"}',
 'B',
 'The article reports the proof that Dijkstra''s algorithm achieves universal optimality for the shortest-path problem across all graph structures.',
 NULL, '2026-03-18 10:40:53', '2026-03-18 10:40:53'),
 
-- ----------------------------------------
-- Article 19: 3D Printing Materials (ME, Easy)
-- ----------------------------------------
(55, 19,
 'What does "sintering" mean in the context of 3D printing metals?',
 '{"A":"Polishing a metal surface to make it shiny","B":"A process of fusing metal powder particles together using heat, without fully melting them, to form a solid object","C":"Painting metal parts with a protective coating","D":"Cutting metal into thin sheets"}',
 'B',
 'Sintering uses heat below the melting point to bond powder particles together, creating a solid structure from metal powders in 3D printing.',
 NULL, '2026-03-18 10:40:54', '2026-03-18 10:40:54'),
 
(56, 19,
 'Based on the article, why is the range of 3D printing materials significant for the future of manufacturing?',
 '{"A":"Because more materials means more colorful products","B":"Because the expanding variety of printable materials enables applications across diverse industries from aerospace to biomedical engineering","C":"Because cheaper materials reduce advertising costs","D":"Because every material prints at the same speed"}',
 'B',
 'The diversity of materials — from metals to carbon fiber to biological materials — broadens 3D printing''s applicability across many engineering fields.',
 NULL, '2026-03-18 10:40:55', '2026-03-18 10:40:55'),
 
(57, 19,
 'What is this article primarily about?',
 '{"A":"How to buy a 3D printer","B":"An overview of the ten most notable materials used in 3D printing and their engineering applications","C":"The history of plastic manufacturing","D":"A comparison of 3D printer brands"}',
 'B',
 'The article surveys ten key materials for 3D printing, briefly describing each material''s properties and potential applications.',
 NULL, '2026-03-18 10:40:56', '2026-03-18 10:40:56'),
 
-- ----------------------------------------
-- Article 20: Nanotechnology (ME, Easy)
-- ----------------------------------------
(58, 20,
 'What does "nanotechnology" refer to as described in this article?',
 '{"A":"Technology used in very small countries","B":"The science and engineering of manipulating materials at the nanometer scale (approximately 1 to 100 nanometers) to achieve novel properties","C":"Technology for viewing distant nano-sized planets","D":"A type of computer software"}',
 'B',
 'Nanotechnology involves working with materials at the atomic and molecular scale where unique physical and chemical properties emerge.',
 NULL, '2026-03-18 10:40:57', '2026-03-18 10:40:57'),
 
(59, 20,
 'Why does the article suggest mechanical engineers should pay attention to nanotechnology?',
 '{"A":"Because nano-sized machines are replacing all conventional machines","B":"Because nanomaterials can enhance the performance and capabilities of products that mechanical engineers design and build","C":"Because nanotechnology courses are mandatory for all engineers","D":"Because nanotechnology is only relevant to chemical engineers"}',
 'B',
 'The article argues that nanomaterials (especially carbon-based ones) are creating new possibilities in product design that directly affect mechanical engineering practice.',
 NULL, '2026-03-18 10:40:58', '2026-03-18 10:40:58'),
 
(60, 20,
 'What is the main message of this article?',
 '{"A":"Nanotechnology is too expensive for practical use","B":"Nanotechnology represents an important new frontier for mechanical engineering, offering enhanced materials and manufacturing capabilities","C":"Only physicists should study nanotechnology","D":"Nanotechnology will replace mechanical engineering entirely"}',
 'B',
 'The article positions nanotechnology as a transformative field that mechanical engineers should engage with to remain competitive.',
 NULL, '2026-03-18 10:40:59', '2026-03-18 10:40:59'),
 
-- ----------------------------------------
-- Article 21: Thermal Energy Storage (ME, Intermediate)
-- ----------------------------------------
(61, 21,
 'What does "thermal energy storage" mean in this article?',
 '{"A":"Storing heat or cold in a medium for later use as an energy resource","B":"Keeping a building warm in winter","C":"The study of how heat moves through walls","D":"A type of solar panel technology"}',
 'A',
 'Thermal energy storage captures heat or cold in materials like molten salt, heated sand, or compressed air for later conversion back to electricity or direct use.',
 NULL, '2026-03-18 10:41:00', '2026-03-18 10:41:00'),
 
(62, 21,
 'Based on the article, what advantage does thermal storage have over lithium-ion batteries for grid-scale energy storage?',
 '{"A":"Thermal storage is always smaller in size","B":"Thermal storage can use inexpensive, abundant materials like sand and salt and can store energy for longer durations at lower cost per unit","C":"Thermal storage produces electricity directly without conversion","D":"Lithium-ion batteries cannot store any energy"}',
 'B',
 'The article highlights that thermal approaches use cheap, abundant materials and are better suited for long-duration storage compared to expensive lithium-ion batteries.',
 NULL, '2026-03-18 10:41:01', '2026-03-18 10:41:01'),
 
(63, 21,
 'What is the central topic of this article?',
 '{"A":"How to insulate a house","B":"Emerging technologies for storing energy as heat or compressed air as alternatives to battery storage for the electrical grid","C":"The physics of temperature measurement","D":"A review of home heating systems"}',
 'B',
 'The article profiles companies and research efforts developing thermal energy storage technologies for grid-scale renewable energy integration.',
 NULL, '2026-03-18 10:41:02', '2026-03-18 10:41:02'),
 
-- ----------------------------------------
-- Article 22: Humanoid Robots (ME, Intermediate)
-- ----------------------------------------
(64, 22,
 'What does "bipedal locomotion" mean as used in this article?',
 '{"A":"Movement using wheels","B":"Walking on two legs, as humans do, which is a significant engineering challenge for robots","C":"Flying using two propellers","D":"Swimming with two fins"}',
 'B',
 'Bipedal locomotion refers to the ability to walk upright on two legs, a complex engineering feat involving balance, dynamics, and control.',
 NULL, '2026-03-18 10:41:03', '2026-03-18 10:41:03'),
 
(65, 22,
 'What can be inferred about why companies like Agility Robotics chose Amazon warehouses as an early deployment environment?',
 '{"A":"Because Amazon offered the highest price","B":"Because warehouses provide structured, repetitive tasks in controlled environments that are well-suited for early-stage humanoid robot deployment","C":"Because robots prefer indoor environments","D":"Because Amazon warehouses have no human workers"}',
 'B',
 'Warehouses offer predictable layouts and repetitive tasks (picking, moving bins) that match current robot capabilities while limiting unpredictable human interactions.',
 NULL, '2026-03-18 10:41:04', '2026-03-18 10:41:04'),
 
(66, 22,
 'What is this article mainly about?',
 '{"A":"The science fiction history of robots","B":"The commercialization of humanoid robots, focusing on Agility Robotics'' Digit and its real-world deployment in warehouse automation","C":"How to program a robot arm","D":"A comparison of robot toys for children"}',
 'B',
 'The article examines the practical deployment of humanoid robots in industrial settings, covering technical, economic, and operational aspects.',
 NULL, '2026-03-18 10:41:05', '2026-03-18 10:41:05'),
 
-- ----------------------------------------
-- Article 23: Robots at Work (ME, Advanced)
-- ----------------------------------------
(67, 23,
 'What does "the second economy" refer to as discussed in this article?',
 '{"A":"The economy of developing countries","B":"A vast digital layer of autonomous processes — algorithms, automated systems, and AI — that operates alongside the traditional human economy","C":"A type of economic recession","D":"The informal cash economy"}',
 'B',
 'Economist W. Brian Arthur''s concept of the second economy describes the growing digital infrastructure of automated processes that execute transactions and decisions without human involvement.',
 NULL, '2026-03-18 10:41:06', '2026-03-18 10:41:06'),
 
(68, 23,
 'What can be inferred about the article''s overall stance on automation and employment?',
 '{"A":"Automation will definitely eliminate all jobs","B":"The article presents multiple expert perspectives showing the impact is uncertain — automation may displace some jobs while creating others, with the net effect depending on policy and adaptation","C":"Automation only affects manufacturing jobs","D":"The article concludes that robots should be banned"}',
 'B',
 'The article deliberately presents both optimistic and pessimistic expert viewpoints, acknowledging deep uncertainty about automation''s long-term labor market effects.',
 NULL, '2026-03-18 10:41:07', '2026-03-18 10:41:07'),
 
(69, 23,
 'What is the primary purpose of this article?',
 '{"A":"To provide instructions for building industrial robots","B":"To examine how robotics and AI-driven automation are reshaping the workforce, presenting multiple expert perspectives on technological unemployment","C":"To review a specific robot product","D":"To compare wages in different industries"}',
 'B',
 'The article surveys expert opinions from economists, engineers, and researchers to explore the complex relationship between automation and human employment.',
 NULL, '2026-03-18 10:41:08', '2026-03-18 10:41:08'),
 
-- ----------------------------------------
-- Article 24: Stellarator Fusion (ME, Advanced)
-- ----------------------------------------
(70, 24,
 'What is a "stellarator" as described in this article?',
 '{"A":"A device for observing stars","B":"A type of nuclear fusion reactor that uses complexly shaped magnetic coils to confine hot plasma in a twisted toroidal configuration","C":"A satellite used for space exploration","D":"A type of solar panel"}',
 'B',
 'A stellarator confines plasma using external magnetic coils arranged in complex 3D geometries, as an alternative to the more common tokamak fusion design.',
 NULL, '2026-03-18 10:41:09', '2026-03-18 10:41:09'),
 
(71, 24,
 'Based on the article, what advantage do stellarators have over tokamaks for fusion energy?',
 '{"A":"Stellarators are cheaper to build","B":"Stellarators can potentially operate in steady-state continuous mode, unlike tokamaks which rely on pulsed plasma currents that are prone to disruptions","C":"Stellarators are smaller","D":"Stellarators do not require any magnets"}',
 'B',
 'The article explains that stellarators achieve confinement through external coil geometry alone, avoiding the instabilities of tokamak plasma currents.',
 NULL, '2026-03-18 10:41:10', '2026-03-18 10:41:10'),
 
(72, 24,
 'What is the main topic of this article?',
 '{"A":"How nuclear power plants generate electricity","B":"The renaissance of stellarator fusion reactor design, featuring Princeton''s Muse prototype and the engineering innovations making stellarators more practical","C":"A comparison of different energy sources","D":"The environmental impact of nuclear waste"}',
 'B',
 'The article profiles the Muse stellarator and broader advancements in stellarator technology including permanent magnets, AI-optimized designs, and 3D printing.',
 NULL, '2026-03-18 10:41:11', '2026-03-18 10:41:11'),
 
-- ----------------------------------------
-- Article 25: NASA Electric Flight (ME+T, Easy)
-- ----------------------------------------
(73, 25,
 'What does "electrified aircraft propulsion" mean in this article?',
 '{"A":"Using electricity from lightning to power planes","B":"Using electric motors and generators, instead of or alongside traditional jet engines, to power aircraft","C":"Charging aircraft batteries at the airport gate","D":"Adding electrical wiring to existing aircraft"}',
 'B',
 'Electrified aircraft propulsion involves partially or fully replacing combustion-based jet engines with electric motor systems for flight.',
 NULL, '2026-03-18 10:41:12', '2026-03-18 10:41:12'),
 
(74, 25,
 'What can be inferred about the biggest engineering challenge for electric aircraft based on this article?',
 '{"A":"Finding pilots willing to fly electric planes","B":"The weight and energy density of current electrical systems, since batteries and motors must be light enough for flight while powerful enough for propulsion","C":"Painting the aircraft in the right color","D":"Building airports large enough for electric planes"}',
 'B',
 'The article emphasizes weight reduction and power density as critical challenges since aircraft have strict weight constraints that electrical systems must meet.',
 NULL, '2026-03-18 10:41:13', '2026-03-18 10:41:13'),
 
(75, 25,
 'What is this article mainly about?',
 '{"A":"NASA''s plans to visit Mars","B":"NASA''s research into electric and hybrid-electric propulsion systems for future commercial aircraft","C":"How airports are powered by solar energy","D":"The history of the Wright brothers'' first flight"}',
 'B',
 'The article describes NASA Glenn Research Center''s efforts to develop electric propulsion technologies for aviation.',
 NULL, '2026-03-18 10:41:14', '2026-03-18 10:41:14'),
 
-- ----------------------------------------
-- Article 26: Eviation Electric Aviation (ME+T, Easy)
-- ----------------------------------------
(76, 26,
 'What does "maiden flight" mean in this article?',
 '{"A":"A flight taken by a young woman","B":"The first flight ever made by a newly built aircraft, marking a key milestone in its development","C":"A flight that takes place in May","D":"A short practice flight before a long journey"}',
 'B',
 'A maiden flight is the inaugural flight of a new aircraft design, proving that the vehicle can actually fly.',
 NULL, '2026-03-18 10:41:15', '2026-03-18 10:41:15'),
 
(77, 26,
 'Why does the article compare the Alice aircraft to conventional turboprop planes?',
 '{"A":"Because turboprops are more beautiful","B":"Because the comparison highlights the potential advantages of electric propulsion — lower operating costs, zero direct emissions, and reduced noise — over conventional aircraft","C":"Because turboprops are also electric","D":"Because both aircraft were made by the same company"}',
 'B',
 'The comparison helps readers understand the specific advantages electric aircraft could offer over existing regional aviation technology.',
 NULL, '2026-03-18 10:41:16', '2026-03-18 10:41:16'),
 
(78, 26,
 'What is the main topic of this article?',
 '{"A":"The history of aviation","B":"The development and significance of Eviation''s all-electric Alice aircraft and its potential to transform regional aviation","C":"How to become a commercial pilot","D":"A comparison of different airline companies"}',
 'B',
 'The article covers the Alice electric aircraft program and its implications for the future of short-haul aviation.',
 NULL, '2026-03-18 10:41:17', '2026-03-18 10:41:17'),
 
-- ----------------------------------------
-- Article 27: Autonomous Eco-Driving (ME+T, Intermediate)
-- ----------------------------------------
(79, 27,
 'What does "deep reinforcement learning" mean in the context of this article?',
 '{"A":"Teaching robots to swim in deep water","B":"A machine learning approach where an AI agent learns optimal behavior through trial and error, receiving rewards for desirable outcomes like reduced fuel consumption","C":"A very thorough study of textbooks","D":"A programming language for autonomous vehicles"}',
 'B',
 'Deep reinforcement learning combines deep neural networks with reward-based learning, enabling the AI to discover optimal driving strategies through simulated experience.',
 NULL, '2026-03-18 10:41:18', '2026-03-18 10:41:18'),
 
(80, 27,
 'What can be inferred about the potential real-world impact of the MIT research described in this article?',
 '{"A":"It will only work in video game simulations","B":"Applying AI-optimized driving strategies at signalized intersections could significantly reduce transportation emissions and fuel costs at scale","C":"The technology requires replacing all existing traffic lights","D":"Only luxury vehicles can use this technology"}',
 'B',
 'The article''s findings — 18% fuel reduction and 25% CO₂ reduction — suggest that widespread deployment of such AI systems could meaningfully reduce transportation''s environmental footprint.',
 NULL, '2026-03-18 10:41:19', '2026-03-18 10:41:19'),
 
(81, 27,
 'What is the main finding described in this article?',
 '{"A":"Traffic lights should be removed from all intersections","B":"MIT researchers used deep reinforcement learning to control autonomous vehicles at intersections, reducing fuel consumption by 18% and CO₂ emissions by 25%","C":"Autonomous vehicles are more dangerous than human-driven cars","D":"Electric vehicles do not need traffic signals"}',
 'B',
 'The article reports on the specific emission and fuel reduction achievements of the AI-based eco-driving system at signalized intersections.',
 NULL, '2026-03-18 10:41:20', '2026-03-18 10:41:20'),
 
-- ----------------------------------------
-- Article 28: Axial-Flux Motor (ME+T, Intermediate)
-- ----------------------------------------
(82, 28,
 'What is the key difference between an "axial-flux" and a "radial-flux" motor as explained in this article?',
 '{"A":"Axial-flux motors are always larger","B":"In axial-flux motors the magnetic flux flows parallel to the axis of rotation, while in radial-flux motors it flows perpendicular — resulting in a flatter, more compact design for axial-flux","C":"Radial-flux motors use permanent magnets while axial-flux motors do not","D":"There is no difference; they are the same thing"}',
 'B',
 'The article explains that the direction of magnetic flux defines the motor type, with axial-flux designs offering superior power density in a thinner package.',
 NULL, '2026-03-18 10:41:21', '2026-03-18 10:41:21'),
 
(83, 28,
 'What can be inferred about why supercar manufacturers chose YASA''s axial-flux motors?',
 '{"A":"Because they are the cheapest motors available","B":"Because their exceptional power density (59 kW/kg) allows high performance while minimizing weight and space — critical factors in performance vehicles","C":"Because they make a louder engine sound","D":"Because supercars do not need powerful motors"}',
 'B',
 'In high-performance vehicles where every kilogram matters, YASA''s motors offer an outstanding power-to-weight ratio that enables both electrification and performance.',
 NULL, '2026-03-18 10:41:22', '2026-03-18 10:41:22'),
 
(84, 28,
 'What is this article primarily about?',
 '{"A":"The history of Ferrari sports cars","B":"YASA''s revolutionary axial-flux electric motors and their adoption by leading supercar manufacturers for hybrid and electric powertrains","C":"How to repair a car engine","D":"A comparison of gasoline and diesel engines"}',
 'B',
 'The article examines YASA''s axial-flux motor technology and its integration into hybrid supercars from Ferrari, Lamborghini, and McLaren.',
 NULL, '2026-03-18 10:41:23', '2026-03-18 10:41:23'),
 
-- ----------------------------------------
-- Article 29: EV Batteries (ME+T, Advanced)
-- ----------------------------------------
(85, 29,
 'What does "solid-state battery" refer to in this article?',
 '{"A":"A battery that never moves","B":"A battery that uses a solid electrolyte instead of the liquid electrolyte found in conventional lithium-ion batteries, potentially offering higher energy density and improved safety","C":"A battery shaped like a solid cube","D":"A battery that is always fully charged"}',
 'B',
 'Solid-state batteries replace the liquid or gel electrolyte with a solid material, which can enable denser energy storage and eliminate flammable liquid components.',
 NULL, '2026-03-18 10:41:24', '2026-03-18 10:41:24'),
 
(86, 29,
 'What can be inferred about the relationship between battery chemistry choices and manufacturing scalability?',
 '{"A":"Any battery chemistry can be easily manufactured at scale","B":"Some promising electrolyte chemistries rely on rare or expensive elements, creating supply chain bottlenecks that could prevent mass production even if the chemistry performs well in the lab","C":"Manufacturing scalability only depends on factory size","D":"Battery chemistry has no effect on cost"}',
 'B',
 'The article discusses how reliance on elements like germanium and tantalum in certain electrolyte chemistries creates supply constraints that affect large-scale manufacturing feasibility.',
 NULL, '2026-03-18 10:41:25', '2026-03-18 10:41:25'),
 
(87, 29,
 'What is the central focus of this article?',
 '{"A":"How to change an electric car battery at home","B":"The materials science and manufacturing challenges of developing solid-state batteries for electric vehicles, analyzing different electrolyte chemistries and their scalability","C":"A comparison of different electric car models","D":"The environmental impact of battery disposal"}',
 'B',
 'The article examines how electrolyte material choices (oxide vs. sulfide) affect both battery performance and the feasibility of large-scale manufacturing.',
 NULL, '2026-03-18 10:41:26', '2026-03-18 10:41:26'),
 
-- ----------------------------------------
-- Article 30: Airbus Superconducting Aircraft (ME+T, Advanced)
-- ----------------------------------------
(88, 30,
 'What does "superconducting" mean in the context of this article''s aircraft technology?',
 '{"A":"An aircraft that flies faster than the speed of sound","B":"Materials that conduct electricity with zero resistance when cooled to extremely low temperatures, enabling ultra-efficient electric motors and power distribution","C":"A type of aircraft fuel","D":"A marketing term for a premium aircraft model"}',
 'B',
 'Superconductors carry electric current without resistance when cooled below a critical temperature, enabling highly efficient power transmission in the proposed aircraft.',
 NULL, '2026-03-18 10:41:27', '2026-03-18 10:41:27'),
 
(89, 30,
 'Based on the article, why does Airbus propose using liquid hydrogen for both fuel and cooling?',
 '{"A":"Because hydrogen is the cheapest fuel available","B":"Because liquid hydrogen at −253°C can simultaneously power fuel cells for electricity generation and cool the superconducting components that require cryogenic temperatures","C":"Because passengers prefer hydrogen-powered aircraft","D":"Because hydrogen is lighter than air and makes the plane float"}',
 'B',
 'The dual-use design elegantly solves two problems at once: hydrogen fuel cells generate electricity while the cryogenic hydrogen maintains the superconducting system''s operating temperature.',
 NULL, '2026-03-18 10:41:28', '2026-03-18 10:41:28'),
 
(90, 30,
 'What is the main subject of this article?',
 '{"A":"Airbus''s new first-class cabin design","B":"Airbus''s ZEROe program to develop a hydrogen fuel-cell powered aircraft using superconducting electrical systems for zero-emission commercial aviation","C":"A history of Airbus as a company","D":"How to reduce turbulence during flights"}',
 'B',
 'The article details Airbus''s ambitious program combining hydrogen fuel cells with superconducting technology to create a zero-emission passenger aircraft.',
 NULL, '2026-03-18 10:41:29', '2026-03-18 10:41:29');
--
-- Dumping data for table `questions`
--

LOCK TABLES `questions` WRITE;
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reading_history`
--

DROP TABLE IF EXISTS `reading_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reading_history` (
  `user_id` int NOT NULL,
  `article_id` int NOT NULL,
  `progress` int DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`article_id`),
  KEY `user_id` (`user_id`),
  KEY `article_id` (`article_id`),
  CONSTRAINT `reading_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `reading_history_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `articles` (`article_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reading_history`
--

LOCK TABLES `reading_history` WRITE;
/*!40000 ALTER TABLE `reading_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `reading_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `tag_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO tags (tag_id, name, slug, deleted_at, created_at, updated_at) VALUES
(1, 'Vocabulary', 'vocabulary', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(2, 'Inference', 'inference', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(3, 'Main Idea', 'main-idea', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(4, 'Detail', 'detail', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(5, 'Academic', 'academic', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(6, 'Technology', 'technology', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(7, 'Engineering', 'engineering', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(8, 'Mathematics', 'mathematics', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(9, 'Computer Science', 'computer-science', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(10, 'Transportation', 'transportation', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(11, 'Sustainability', 'sustainability', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(12, 'AI & Machine Learning', 'ai-machine-learning', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(13, 'Materials Science', 'materials-science', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(14, 'Energy', 'energy', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(15, 'Construction', 'construction', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(16, 'Robotics', 'robotics', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(17, 'Data Science', 'data-science', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(18, 'Environmental', 'environmental', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(19, 'Structural Engineering', 'structural-engineering', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00'),
(20, 'Automotive', 'automotive', NULL, '2026-03-18 10:00:00', '2026-03-18 10:00:00');
--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO users (user_id, name, email, password, deleted_at, created_at, updated_at) VALUES
(1, 'Alice Chen', 'alice.chen@gmail.com', '001', NULL, '2026-03-16 10:00:00', '2026-03-16 10:00:00'),
(2, 'Bob Li', 'bob.li@outlook.com', '002', NULL, '2026-03-16 10:01:00', '2026-03-16 10:01:00'),
(3, 'Cathy Wang', 'cathy.wang@163.com', '003', NULL, '2026-03-16 10:02:00', '2026-03-16 10:02:00');
--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vocabulary_notes`
--

DROP TABLE IF EXISTS `vocabulary_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vocabulary_notes` (
  `vocabulary_note_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `word` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `definition` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `example` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`vocabulary_note_id`),
  KEY `fk_vocabulary_notes_users1_idx` (`user_id`),
  CONSTRAINT `fk_vocabulary_notes_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vocabulary_notes`
--

LOCK TABLES `vocabulary_notes` WRITE;
/*!40000 ALTER TABLE `vocabulary_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `vocabulary_notes` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-16 16:34:19
