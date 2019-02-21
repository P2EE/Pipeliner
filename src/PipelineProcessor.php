<?php
namespace p2ee\Pipeliner;

interface PipelineProcessor {

    public function getResult(): string;

    public function getInput(): array;

    public function run();
}