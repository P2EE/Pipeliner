<?php
namespace p2ee\Pipeliner;

class PipelineGeneratorTest extends \PHPUnit\Framework\TestCase {

    public function testBuild() {

        $gen = new PipelineGenerator([
            new A(),
            new B(),
            new C(),
            new D(),
            new D2(),
            new E(),
        ]);

        $result = $gen->build([RootData::class, RootData2::class]);

        $expected = [
            [
                A::class,
                B::class
            ],
            [
                C::class,
                D::class,
            ],
            [
                D2::class,
                E::class,
            ]
        ];

        $this->assertEquals($expected, $result);
    }
}


class RootData {

}

class RootData2 {

}

class AResult {

}

class BResult {

}

class CResult {

}

class DResult {

}

class EResult {

}

class A implements PipelineProcessor {

    public function getResult(): string {
        return AResult::class;
    }

    public function getInput(): array {
        return [RootData::class];
    }

    public function run() {
        return new AResult();
    }
}

class B implements PipelineProcessor {

    public function getResult(): string {
        return BResult::class;
    }

    public function getInput(): array {
        return [RootData::class, RootData2::class];
    }

    public function run() {
        return new BResult();
    }
}

class C implements PipelineProcessor {

    public function getResult(): string {
        return CResult::class;
    }

    public function getInput(): array {
        return [BResult::class];
    }

    public function run() {
        return new CResult();
    }
}

class D implements PipelineProcessor {

    public function getResult(): string {
        return DResult::class;
    }

    public function getInput(): array {
        return [BResult::class, RootData::class];
    }

    public function run() {
        return new DResult();
    }
}

class D2 implements PipelineProcessor {

    public function getResult(): string {
        return DResult::class;
    }

    public function getInput(): array {
        return [CResult::class];
    }

    public function run() {
        return new DResult();
    }
}

class E implements PipelineProcessor {

    public function getResult(): string {
        return EResult::class;
    }

    public function getInput(): array {
        return [DResult::class];
    }

    public function run() {
        return new EResult();
    }
}