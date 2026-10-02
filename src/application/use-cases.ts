import type { EvalnorCoreContext, EvalnorCorePort } from '../platform/evalnor-core.js';
import type { CandidateApplication } from '../domain/model.js';
import type { CandidateApplicationRepository } from './repository.js';
import { canProcessCandidate } from '../domain/services.js';
import { forbidden, validationError } from './errors.js';
export class AdvanceCandidate { constructor(private repo: CandidateApplicationRepository, private core: EvalnorCorePort) {} async execute(ctx: EvalnorCoreContext, application: CandidateApplication, stage: CandidateApplication['stage']) { if (!(await this.core.authorize(ctx,'talent.applications.manage'))) throw forbidden(); if (!canProcessCandidate(application)) throw validationError('Candidate consent is required',{field:'consentRecordedAt'}); const updated={...application,stage}; await this.repo.save(updated); await this.core.audit(ctx,{action:'talent.application.stage_changed',entityType:'CandidateApplication',entityId:application.id,occurredAt:new Date().toISOString(),metadata:{stage}}); return updated; } }
