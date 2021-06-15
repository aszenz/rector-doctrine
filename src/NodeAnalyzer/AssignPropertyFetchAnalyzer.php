<?php

declare(strict_types=1);

namespace Rector\Doctrine\NodeAnalyzer;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt\ClassMethod;
use Rector\Core\PhpParser\Node\BetterNodeFinder;
use Rector\Doctrine\ValueObject\AssignToPropertyFetch;
use Rector\NodeNameResolver\NodeNameResolver;

final class AssignPropertyFetchAnalyzer
{
    public function __construct(
        private BetterNodeFinder $betterNodeFinder,
        private NodeNameResolver $nodeNameResolver,
    ) {
    }

    /**
     * @return AssignToPropertyFetch[]
     */
    public function resolveAssignToPropertyFetch(ClassMethod $classMethod): array
    {
        /** @var Assign[] $assigns */
        $assigns = $this->betterNodeFinder->findInstanceOf((array) $classMethod->stmts, Assign::class);

        $assignsToPropertyFetch = [];
        foreach ($assigns as $assign) {
            if (! $assign->var instanceof PropertyFetch) {
                continue;
            }

            $propertyFetch = $assign->var;

            /** @var string $propertyName */
            $propertyName = $this->nodeNameResolver->getName($propertyFetch);

            $assignsToPropertyFetch[] = new AssignToPropertyFetch($assign, $propertyFetch, $propertyName);
        }

        return $assignsToPropertyFetch;
    }
}
