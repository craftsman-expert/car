import { PerformerInterface } from './PerformerInterface'

export interface AudioInterface {
  id: number;
  name: string;
  performer: PerformerInterface;
  src: string;
  like: boolean;
  playing?: boolean;
}
