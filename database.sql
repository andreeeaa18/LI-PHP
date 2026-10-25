

CREATE DATABASE IF NOT EXISTS li_blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE li_blog;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image_url VARCHAR(500) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    parent_id INT DEFAULT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    reason VARCHAR(255) DEFAULT 'Inappropriate content',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS drawings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    cell_data TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


INSERT IGNORE INTO users (name, email, password, role) VALUES 
('Admin', 'admin@li.com', '$2y$12$TivxxozaywOSCW.na8R6C.u3VdZIpk0jjBk9KMU4p1Hma9xYAn0y6', 'admin');

INSERT IGNORE INTO users (name, email, password, role) VALUES 
('Alice', 'alice@li.com', '$2y$12$TivxxozaywOSCW.na8R6C.u3VdZIpk0jjBk9KMU4p1Hma9xYAn0y6', 'user'),
('Bob', 'bob@li.com', '$2y$12$TivxxozaywOSCW.na8R6C.u3VdZIpk0jjBk9KMU4p1Hma9xYAn0y6', 'user');

INSERT IGNORE INTO posts (id, user_id, title, content, image_url, created_at) VALUES
(1, 2, 'Why Procrastinators Procrastinate',
'pro-cras-ti-na-tion |prəˌkrastəˈnāSHən, prō-|\nnoun\nthe action of delaying or postponing something: your first tip is to avoid procrastination.\n\nWho would have thought that after decades of struggle with procrastination, the dictionary, of all places, would hold the solution.\n\nAvoid procrastination. So elegant in its simplicity.\n\nWhile we\'re here, let\'s make sure obese people avoid overeating, depressed people avoid apathy, and someone please tell beached whales that they should avoid being out of the ocean.\n\nNo, "avoid procrastination" is only good advice for fake procrastinators—those people that are like, "I totally go on Facebook a few times every day at work—I\'m such a procrastinator!" The same people that will say to a real procrastinator something like, "Just don\'t procrastinate and you\'ll be fine."\n\nThe thing that neither the dictionary nor fake procrastinators understand is that for a real procrastinator, procrastination isn\'t optional—it\'s something they don\'t know how to not do.\n\nIn college, the sudden unbridled personal freedom was a disaster for me—I did nothing, ever, for any reason. The one exception was that I had to hand in papers from time to time. I would do those the night before, until I realized I could just do them through the night, and I did that until I realized I could actually start them in the early morning on the day they were due. This behavior reached caricature levels when I was unable to start writing my 90-page senior thesis until 72 hours before it was due, an experience that ended with me in the campus doctor\'s office learning that lack of blood sugar was the reason my hands had gone numb and curled up against my will. (I did get the thesis in—no, it was not good.)\n\nEven this post took much longer than it should have, because I spent a bunch of hours doing things like seeing this picture sitting on my desktop from a previous post, opening it, looking at it for a long time thinking about how easily he could beat me in a fight, then wondering if he could beat a tiger in a fight, then wondering who would win between a lion and a tiger, and then googling that and reading about it for a while (the tiger would win). I have problems.',
'https://waitbutwhy.com/wp-content/uploads/2013/10/FEATURE6-300x203.png',
'2026-01-05 10:00:00'),

(2, 3, 'How to Pick Your Life Partner',
'Dissatisfied single people should actually consider themselves in a neutral, fairly hopeful position, compared to what their situation could be. A single person who would like to find a great relationship is one step away from it, with their to-do list reading, "1) Find a great relationship." People in unhappy relationships, on the other hand, are three leaps away, with a to-do list of "1) Go through a soul-crushing break-up. 2) Emotionally recover. 3) Find a great relationship." Not as bad when you look at it that way, right?\n\nAll the research on how vastly happiness varies between happy and unhappy marriages makes perfect sense, of course. It\'s your life partner.\n\nThinking about how overwhelmingly important it is to pick the right life partner is like thinking about how huge the universe really is or how terrifying death really is—it\'s too intense to internalize the reality of it, so we just don\'t think about it that hard and remain in slight denial about the magnitude of the situation.\n\nBut unlike death and the universe\'s size, picking a life partner is fully in your control, so it\'s critical to make yourself entirely clear on how big a deal the decision really is and to thoroughly analyze the most important factors in making it.\n\nSo how big a deal is it?\n\nWell, start by subtracting your age from 90. If you live a long life, that\'s about the number of years you\'re going to spend with your current or future life partner, give or take a few.\n\nI\'m pretty sure no one over 80 reads Wait But Why, so no matter who you are, that\'s a lot of time—and almost the entirety of the rest of your one existence.\n\n(Sure, people get divorced, but you don\'t think you will. A recent study shows that 86% of young people assume their current or future marriage will be forever, and I doubt older people feel much differently. So we\'ll proceed under that assumption.)\n\nAnd when you choose a life partner, you\'re choosing a lot of things, including your parenting partner and someone who will deeply influence your children, your eating companion for about 20,000 meals, your travel companion for about 100 vacations, your primary leisure time and retirement friend, your career therapist, and someone whose day you\'ll hear about 18,000 times.\n\nIntense shit.\n\nSo given that this is by far the most important thing in life to get right, how is it possible that so many good, smart, otherwise-logical people end up choosing a life partnership that leaves them dissatisfied and unhappy?',
'https://waitbutwhy.com/wp-content/uploads/2014/02/non-vday-staircase.png',
'2026-01-12 14:30:00'),

(3, 1, 'Religion for the Nonreligious',
'The mind…can make a heaven of hell, a hell of heaven. ― John Milton\n\nThe mind is certainly its own cosmos. — Alan Lightman\n\nYou go to school, study hard, get a degree, and you\'re pleased with yourself. But are you wiser?\n\nYou get a job, achieve things at the job, gain responsibility, get paid more, move to a better company, gain even more responsibility, get paid even more, rent an apartment with a parking spot, stop doing your own laundry, and you buy one of those $9 juices where the stuff settles down to the bottom. But are you happier?\n\nYou do all kinds of life things—you buy groceries, read articles, get haircuts, chew things, take out the trash, buy a car, brush your teeth, shit, sneeze, shave, stretch, get drunk, put salt on things, have sex with someone, charge your laptop, jog, empty the dishwasher, walk the dog, buy a couch, close the curtains, button your shirt, wash your hands, zip your bag, set your alarm, fix your hair, order lunch, act friendly to someone, watch a movie, drink apple juice, and put a new paper towel roll on the thing.\n\nBut as you do these things day after day and year after year, are you improving as a human in a meaningful way?\n\nIn the last post, I described the way my own path had led me to be an atheist—but how in my satisfaction with being proudly nonreligious, I never gave serious thought to an active approach to internal improvement—hindering my own evolution in the process.\n\nThis wasn\'t just my own naiveté at work. Society at large focuses on shallow things, so it doesn\'t stress the need to take real growth seriously. The major institutions in the spiritual arena—religions—tend to focus on divinity over people, making salvation the end goal instead of self-improvement. The industries that do often focus on the human condition—philosophy, psychology, art, literature, self-help, etc.—lie more on the periphery, with their work often fragmented from each other. All of this sets up a world that makes it hard to treat internal growth as anything other than a hobby, an extra-curricular, icing on the life cake.\n\nConsidering that the human mind is an ocean of complexity that creates every part of our reality, working on what\'s going on in there seems like it should be a more serious priority. In the same way a growing business relies on a clear mission with a well thought-out strategy and measurable metrics, a growing human needs a plan—if we want to meaningfully improve, we need to define a goal, understand how to get there, become aware of obstacles in the way, and have a strategy to get past them.',
'https://waitbutwhy.com/wp-content/uploads/2014/10/chaotic-brain-FEATURE1-345x234.jpg',
'2026-01-20 09:15:00'),

(4, 2, 'How to Name a Baby',
'The first time a friend of mine had a child, it was intensely jarring.\n\nI\'d be living my normal day, and then the thought would hit me—"Matt has a son"—and my whole world would get turned upside down.\n\nThree years and six friend babies later, I\'m 32 and have numbed to the whole thing considerably. It\'s still weird. But not jarring.\n\nThis new phenomenon in my life has introduced several new experiences—things like "having your feelings hurt and losing self-confidence because your friend\'s toddler doesn\'t like you" and "learning that talking about the baby as a \'toy\' or a \'pod\' and commenting on \'it not having a brain yet\' is less funny to the baby\'s parents than it is to you."\n\nParents choosing a name have a few options:\n\n1) Go Timeless\n\nExamples: John, James, William, David, Mary, Sarah, Elizabeth\n\nBenefits: You won\'t embarrass yourself; You won\'t pigeonhole the kid in any way, including generationally; It\'s classy; There\'s something cool about a common bond with centuries of previous humans\n\nDrawbacks: It\'s kind of boring.\n\nThese names are often biblical, or sometimes those of famous royalty, and they\'re bigger than any one generation—William is not a typical old man\'s name or young guy\'s name—it\'s just William. And they\'re always popular.\n\n2) Go Super Weird\n\nExamples: Winter, Namaste, Jameliah, Stormy, Cameo, Grudzel\n\nBenefits: No one will ever question your balls; If the kid is awesome, then it\'s awesome.\n\nDrawbacks: They\'ll have to spell out their name on phone calls 2 trillion times throughout their life; They\'ll have to watch people figuring out how to react every time they introduce themselves; They\'ll get made fun of at school; It might hurt their chances of getting job interviews.\n\nDespite several drawbacks, it\'s a nice chance to say, "P.S. We don\'t give a shit about what other people think." And again, if the kid\'s awesome, a weird name just makes them even more awesome.\n\nFor what it\'s worth, a lot more people are going weird now than they used to. People used to be almost uniformly conformist. In 1950, only 5% of parents strayed out of the Top 1,000 names when naming their child. In 2012, 27% of parents went weird and left the Top 1,000.',
'https://waitbutwhy.com/wp-content/uploads/2013/12/FEATURE5-300x203.png',
'2026-02-03 11:45:00'),

(5, 3, 'How to Pick a Career (That Actually Fits You)',
'Hey readers! Quick note before we jump in:\n\nThis is a post about something I\'ve been wanting to write about forever: careers. Society tells us a lot of things about what we should want in a career and what the possibilities are—which is weird because I\'m pretty sure society knows very little about any of this. When it comes to careers, society is like your great uncle who traps you at holidays and goes on a 15-minute mostly incoherent unsolicited advice monologue, and you tune out almost the whole time because it\'s super clear he has very little idea what he\'s talking about and that everything he says is like 45 years outdated. Society is like that great uncle, and conventional wisdom is like his rant. Except in this case, instead of tuning it out, we pay rapt attention to every word, and then we make major career decisions based on what he says. Kind of a weird thing for us to do.\n\nThis post isn\'t me giving you career advice really—it\'s a framework that I think can help you make career decisions that actually reflect who you are, what you want, and what our rapidly changing career landscape looks like today.\n\nYour Life Path So Far\n\nFor most of us, childhood is kind of like a river, and we\'re kind of like tadpoles.\n\nWe didn\'t choose the river. We just woke up out of nowhere and found ourselves on some path set for us by our parents, by society, and by circumstances. We\'re told the rules of the river and the way we should swim and what our goals should be. Our job isn\'t to think about our path—it\'s to succeed on the path we\'ve been placed on, based on the way success has been defined for us.\n\nFor many of us, our childhood river then feeds into a pond, called college. We may have some say in which particular pond we landed in, but in the end, most college ponds aren\'t really that different from one another.\n\nAnd then, 22 years after waking up in a rushing river, we\'re kicked out of the pond and told by the world to go make something of our lives.\n\nWhen scientists study people on their deathbed and how they feel about their lives, they usually find that many of them feel some serious regrets. I think a lot of those regrets stem from the fact that most of us aren\'t really taught about path-making in our childhoods, and most of us also don\'t get much better at path-making as adults, which leaves many people looking back on a life path that didn\'t really make sense, given who they are and the world they lived in.\n\nSo this is a post about path-making.',
'https://waitbutwhy.com/wp-content/uploads/2018/04/career-feature-1-300x203.png',
'2026-02-14 16:00:00'),

(6, 1, 'Mailbag #1',
'I get a lot of email from readers asking great questions, and there\'s just not time to give most of them a proper answer. Unless, of course, I do a full post dedicated to those questions. So that\'s what this is.\n\nWhen the last post went out, I asked our email subscribers to submit mailbag questions, and a lot of you responded. I can group the responses that came in into two categories:\n\n1) Questions whose answers would be somewhere between one sentence and a few paragraphs\n\n2) Questions whose answers would be somewhere between a few paragraphs and 30,000 words\n\nThe latter went into my Potential Future Posts List. The former were perfect for the mailbag.\n\nFor this mailbag, I started with 46 questions and saved the rest for future mailbags. Moving forward, you can submit a mailbag question anytime to mailbag@waitbutwhy.com.\n\nOkay let\'s jump in—\n\n___________\n\nSmooth or crunchy peanut butter? – James T. (Adelaide, Australia)\n\nSmooth. I have a jar of peanut butter and sometimes I take a big spoon of it and eat it, and the next 45 seconds of my life is chaotic and upsetting. Crunchy adds another factor on top of that situation, which is the last thing I need.\n\nWhat do you believe that not many other people do? – Jeremiah O. (New York, NY)\n\nSaying you\'re purely pro-life or purely pro-choice are both inane, for different reasons.\n\nBeing inside is better than being outside in not all but most cases.\n\nThe longer the plane ride, the better. Plane rides are the best—as long as I don\'t have to work or have to sleep.\n\nGreat music without lyrics is better than great music with lyrics.\n\nBlack licorice is delicious.\n\nChocolate is a bad flavor of ice cream.\n\nAnimated TV or movies are more fun to watch than real people TV or movies.\n\nDo you know how to code? – Dawid P. (Sufczyn, Poland)\n\nNo. But I do know BC Calculus, thank god, because the American public school curriculum was last updated in 1820.\n\nPart of me thinks it\'s a no-brainer time investment to learn to code, but part of me also wonders if we might be in a phase of time when it\'s an important skill to have, but then that phase ends. I should probably learn how to code.',
'https://waitbutwhy.com/wp-content/uploads/2016/08/FEATURE-300x203.png',
'2026-03-01 08:30:00');
