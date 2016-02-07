<?php

namespace Loposum\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Sepia\PoParser;
use Sepia\FileHandler;

class TranslateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('translate')
            ->setDescription('Populate untranslated entries in a PO file with lorem ipsum')
            ->addArgument('from', InputArgument::REQUIRED, 'Which file should be translated?')
            ->addArgument('to', InputArgument::REQUIRED, 'Where should the translated file be output to?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $inFile = new FileHandler($input->getArgument('from'));
        $outFileLocation = $input->getArgument('to');
        $poParser = new PoParser($inFile);

        $messages = $poParser->parse($inFile);

        foreach ($messages as $msgid => $message) {
            $message = $this->translateMessage($message);
            $poParser->setEntry($msgid, $message, false);
        }
        $poParser->writeFile($outFileLocation);
    }

    private function translateMessage($message)
    {
        foreach ($message['msgid'] as $lineNo => $messageLine) {
            $messageKey = 'msgstr';
            if (array_key_exists('msgid_plural', $message)) {
                $messageKey = 'msgstr[0]';
            }
            if (!$message[$messageKey][$lineNo]) {
                $message[$messageKey][$lineNo] = preg_replace_callback('/(\w{2,})/', [$this, 'translateText'], $messageLine);
            }
        }
        return $message;
    }

    private function translateText($text)
    {
        return str_rot13(array_shift($text));
    }
}
