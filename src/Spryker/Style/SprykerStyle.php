<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Style;

use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;

class SprykerStyle extends SymfonyStyle
{
    /**
     * @var \Symfony\Component\Console\Output\BufferedOutput
     */
    protected $bufferedOutput;

    /**
     * @var int
     */
    protected $lineLength;

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->bufferedOutput = new BufferedOutput($output->getVerbosity(), false, clone $output->getFormatter());
        $width = (new Terminal())->getWidth() ?: self::MAX_LINE_LENGTH;
        $this->lineLength = min($width - (int)(DIRECTORY_SEPARATOR === '\\'), self::MAX_LINE_LENGTH);

        parent::__construct($input, $output);
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function title($message)
    {
        $message = $message . str_pad(' ', $this->lineLength - Helper::strlenWithoutDecoration($this->getFormatter(), $message));

        $this->newLine();
        $this->writeln([
            str_repeat('=', Helper::strlenWithoutDecoration($this->getFormatter(), $message)),
            OutputFormatter::escapeTrailingBackslash($message),
            str_repeat('=', Helper::strlenWithoutDecoration($this->getFormatter(), $message)),
        ]);
        $this->newLine();
    }

    /**
     * {@inheritdoc}
     */
    public function section($message)
    {
        $message = sprintf('Section: <fg=green>%s</>', $message);
        $messageLength = $this->lineLength - Helper::strlenWithoutDecoration($this->getFormatter(), $message);
        $message = $message . str_pad(' ', $messageLength);

        $this->newLine();
        $this->writeln([
            str_repeat('*', Helper::strlenWithoutDecoration($this->getFormatter(), $message)),
            str_repeat(' ', Helper::strlenWithoutDecoration($this->getFormatter(), $message)),
            OutputFormatter::escapeTrailingBackslash($message),
            str_repeat(' ', Helper::strlenWithoutDecoration($this->getFormatter(), $message)),
            str_repeat('*', Helper::strlenWithoutDecoration($this->getFormatter(), $message)),
        ]);
        $this->newLine();
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function text($message)
    {
        $messageLength = $this->lineLength - Helper::strlenWithoutDecoration($this->getFormatter(), $message);
        $message = $message . str_pad(' ', $messageLength);

        $this->autoPrependText();
        $this->writeln([
            $message,
            str_repeat('=', Helper::strlenWithoutDecoration($this->getFormatter(), $message)),
        ]);
    }

    /**
     * @param string $question
     * @param array $choices
     * @param string|int|null $default
     *
     * @return bool|mixed|null|string
     */
    public function choice($question, array $choices, $default = null)
    {
        if ($default) {
            $values = array_flip($choices);
            $default = $values[$default];
        }
        $question = new ChoiceQuestion($question, $choices, $default);
        $question->setMultiselect(true);

        return $this->askQuestion($question);
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function note($message)
    {
        $this->newLine();
        $this->writeln(sprintf(' <fg=green>//</> <fg=yellow>%s</>', $message));
        $this->newLine();
    }

    /**
     * @return void
     */
    protected function autoPrependText()
    {
        $fetched = $this->bufferedOutput->fetch();
        if ("\n" !== substr($fetched, -1)) {
            $this->newLine();
        }
    }
}
