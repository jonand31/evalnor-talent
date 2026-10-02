import type { CandidateApplication } from '../domain/model.js';
export interface CandidateApplicationRepository { findById(id: string): Promise<CandidateApplication | null>; save(application: CandidateApplication): Promise<void>; }
