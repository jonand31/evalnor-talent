export interface CandidateApplication {
  id: string;
  candidateId: string;
  jobId: string;
  stage: 'new' | 'screening' | 'interview' | 'offer' | 'placed' | 'rejected' | 'withdrawn';
  consentRecordedAt?: string;
  ownerUserId?: string;
}
