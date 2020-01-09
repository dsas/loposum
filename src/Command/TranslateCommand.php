<?php

namespace Loposum\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Sepia\PoParser\Parser;
use Sepia\PoParser\SourceHandler\FileSystem;
use Sepia\PoParser\PoCompiler;

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
        $inFile = new FileSystem($input->getArgument('from'));
        $outFileLocation = $input->getArgument('to');
        $poParser = new Parser($inFile);

        $catalog = $poParser->parse();

        foreach ($catalog->getEntries() as $entry) {
            $this->translateMessage($entry);
        }

        $poCompiler = new PoCompiler();
        $compiledContents = $poCompiler->compile($catalog);

        file_put_contents($outFileLocation, $compiledContents);
    }

    private function translateMessage($entry)
    {
        $fakedTranslation = false;
        if (!$entry->getMsgId()) {
            return false;
        }

        $text = $entry->getMsgStr();
        if (!$text) {
            $text = $this->translateText($entry->getMsgId());
            $entry->setMsgStr($text);
            $fakedTranslation = true;
        }
        if ($entry->isPlural()) {
            $messageID = $entry->getMsgIdPlural();
            $messageTexts = $entry->getMsgStrPlurals();
            foreach ($messageTexts as $key => &$text) {
                if (!$text) {
                    $fakedTranslation = true;
                    $text = $this->translateText($messageID);
                }
            }
            $entry->setMsgStrPlurals($messageTexts);
        }
        return $fakedTranslation;
    }

    private function translateText($text)
    {
        return preg_replace_callback(
            '/\\\\([A-Za-z])/',
            // Undo control characters
            function ($matches) {return str_rot13($matches[0]); }, 
            str_rot13($text)
        );
    }
}
