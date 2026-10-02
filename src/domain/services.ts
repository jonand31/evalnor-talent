import type { CandidateApplication } from './model.js';

export const canProcessCandidate = (a: CandidateApplication) => Boolean(a.consentRecordedAt);
export const isSuccessfulPlacement = (a: CandidateApplication) => a.stage === 'placed';
