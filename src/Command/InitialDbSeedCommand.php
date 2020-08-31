<?php
namespace App\Command;

use App\Entity\Joke;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitialDbSeedCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:initial-seed-db';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var array
     */
    private $jokes = array(
        "What do you call a bee that can’t make up its mind? A maybe.",
        "How much money does a pirate pay for corn? A buccaneer.",
        "What do you call a pig that does karate? A pork chop.",
        "What do you call an everyday potato? A commentator.",
        "What’s the difference between a hippo and a Zippo? A hippo is really heavy, and a Zippo is a little lighter.",
        "What did the atom say after losing an electron? 'I really gotta keep an ion them.'",
        "Why did the scarecrow win an award? Because he was outstanding in his field.",
        "Did you hear about the cheese factory that exploded in France? There was nothing left but de Brie.",
        "Why did the can crusher quit his job? Because it was soda pressing.",
        "How do astronomers organize a party? They planet.",
        "Knock, knock., Who’s there? Olive., Olive, who? Olive you so much.",
        "Knock, knock, Who’s there? Norma Lee, Norma Lee, who? Norma Lee I don't say this, but I think I'm falling in love with you.",
        "Knock, knock., Who’s there? Honeydew. Honeydew, who? Honeydew you know how much I love you?",
        "Knock, knock., Who’s there? Juno. Juno, who? Juno that you’re the love of my life?",
        "Knock, knock., Who’s there? Lena. Lena, who? Lena little closer so I can kiss you.",
        "Knock, knock., Who’s there? Ben. Ben, who? Ben thinking about you all day.",
        "Knock, knock., Who’s there? Muffin. Muffin, who? Muffin in this world can keep us apart.",
        "Knock, knock., Who’s there? Ivana. Ivana, who? Ivana spend the rest of my life with you.",
        "Knock, knock., Who’s there? Snow. Snow, who? Snow use, I just can’t stop thinking about you.",
        "Knock, knock., Who’s there? Anita. Anita, who? Anita kiss from you."
    );

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();

        $this->em = $em;
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Seed the DB')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command will seed the DB');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
                             'Seeding DB',
                             '============',
                             '',
                         ]);
        try {
            foreach ($this->jokes as $joke) {
                $output->writeln("Inserting " . $joke);
                // create new Joke entity
                $jokeEntity = (new Joke())
                    ->setJoke($joke);

                $this->em->persist($jokeEntity);
            }

            $this->em->flush();

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $output->writeln([$e->getMessage()]);

            return Command::FAILURE;
        }
    }
}
