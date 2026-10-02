import type { CandidateApplication } from '../../domain/model.js';
import type { CandidateApplicationRepository } from '../../application/repository.js';
export class InMemoryCandidateApplicationRepository implements CandidateApplicationRepository { private items=new Map<string,CandidateApplication>(); async findById(id:string){return this.items.get(id) ?? null;} async save(item:CandidateApplication){this.items.set(item.id,item);} }
