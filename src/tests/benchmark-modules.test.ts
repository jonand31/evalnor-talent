import { strict as assert } from 'node:assert'; import test from 'node:test'; import { onboardingProgress } from '../domain/onboarding/services.js';
test('calculates onboarding progress',()=>{assert.equal(onboardingProgress(['a'],{id:'o',workerId:'w',templateId:'t',startsAt:'x',taskIds:['a','b'],status:'active'}),.5);});
