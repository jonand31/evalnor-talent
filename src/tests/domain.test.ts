import { strict as assert } from 'node:assert'; import test from 'node:test'; import { canProcessCandidate,isSuccessfulPlacement } from '../domain/services.js';
test('candidate consent and placement rules',()=>{const a={id:'a',candidateId:'c',jobId:'j',stage:'placed' as const,consentRecordedAt:'x'};assert.equal(canProcessCandidate(a),true);assert.equal(isSuccessfulPlacement(a),true);});
