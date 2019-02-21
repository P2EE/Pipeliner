<?php
namespace p2ee\Pipeliner;

class PipelineGenerator {

    /**
     * @var PipelineProcessor[]
     */
    protected $processors = [];

    public function __construct(array $processors) {
        $this->processors = $processors;
    }

    public function build($initialData = []) {
        $resultsList = [];
        $inputLists = [];
        $processorNames = [];

        foreach ($this->processors as $processor) {
            $processorName = get_class($processor);
            $processorNames[] = $processorName;

            $inputLists[$processorName] = $processor->getInput();
        }

        foreach ($this->processors as $processor) {
            $processorName = get_class($processor);
            $resultsList[$processorName] = $processor->getResult();
        }

        $starter = $this->getRunnableProcessors($processorNames, $initialData, $inputLists);

        $availableInputs = [];
        foreach ($initialData as $data) {
            $availableInputs[$data] = $data;
        }
        foreach ($starter as $starterName) {
            $availableInputs[$resultsList[$starterName]] = $resultsList[$starterName];
        }

        $processorRunnable = $starter;

        $rounds = [$starter];

        while (true) {
            $runnableProcessors = $this->getRunnableProcessors($processorNames, $availableInputs, $inputLists);
            $new = array_values(array_diff($runnableProcessors, $processorRunnable));
            $processorRunnable = array_merge($processorRunnable, $new);

            if (count($new) === 0) {
                break;
            }
            $rounds[] = $new;

            foreach ($new as $processorName) {
                $availableInputs[$resultsList[$processorName]] = $resultsList[$processorName];
            }

            if (count($runnableProcessors) === count($processorNames)) {
                break;
            }
        }

//        $notRunnableProcessors = array_diff($processorNames, $processorRunnable);

        return $rounds;
    }

    protected function getRunnableProcessors($processorList, $availableInputs, $inputLists) {
        return array_filter($processorList, function ($name) use ($availableInputs, $inputLists) {
            $inputs = $inputLists[$name];
            if (count($inputs) > count($availableInputs)) {
                return false;
            }

            foreach ($inputs as $resultName) {
                if (!in_array($resultName, $availableInputs)) {
                    return false;
                }
            }
            return true;
        });
    }
}
